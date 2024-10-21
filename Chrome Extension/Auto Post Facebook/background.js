const mobileUserAgent = "Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1";






let tabId = null;
const inList=(list, str)=>list.map(e=>e.includes(str)).reduce((a,b)=>a||b);
const checkTabs=(groupId)=>{
    console.log('checkTabs');
    chrome.tabs.query({},(tabs)=> {
        let found = inList(tabs.map(tab=>tab.url).filter(t=>(t!=undefined && t!=null)), '/'+groupId+'/');
        console.log('found', found)
        if ( !found ){
            if( tabId != null ){
                try{
                    chrome.tabs.remove( tabId );
                }catch(e){}
            }

            chrome.tabs.create({
                url:'https://www.facebook.com/groups/'+groupId+'/'
            }, function(tab){
                tabId = tab.id;

                chrome.debugger.detach({tabId: tabId})
                chrome.debugger.attach({tabId: tabId}, "1.3", function() {
                    chrome.debugger.sendCommand({tabId: tabId}, "Network.enable");
                    chrome.debugger.onEvent.addListener(function(source, method, params) {
                        if (method === "Network.webSocketFrameReceived" || method === "Network.webSocketFrameSent") {
                            // console.log({method, payloadData: params.response.payloadData});
                            
                            const payloadData = params.response.payloadData;
                            const binaryString = atob(payloadData);

                            let bufferSizeInBytes = binaryString.length;
                            let buffer = new ArrayBuffer(bufferSizeInBytes);

                            let uint8View = new Uint8Array(buffer);
                            for (let i = 0; i < binaryString.length; i++) {
                                uint8View[i] = binaryString.charCodeAt(i);
                            }
                            console.log({method, payloadData, buffer});
                        }
                    });
                });
            });
        }else{
            tabs.forEach(tab=>{
                if( tabId == null && tab.url != undefined ){
                    if( tab.url.includes(groupId) ){
                        tabId = tab.id;
                        
                        chrome.debugger.detach({tabId: tabId})
                        chrome.debugger.attach({tabId: tabId}, "1.3", function() {
                            chrome.debugger.sendCommand({tabId: tabId}, "Network.enable");
                            chrome.debugger.onEvent.addListener(function(source, method, params) {
                                if (method === "Network.webSocketFrameReceived" || method === "Network.webSocketFrameSent") {
                                    // console.log({method, payloadData: params.response.payloadData});
                                    
                                    const payloadData = params.response.payloadData;
                                    const binaryString = atob(payloadData);
        
                                    let bufferSizeInBytes = binaryString.length;
                                    let buffer = new ArrayBuffer(bufferSizeInBytes);
        
                                    let uint8View = new Uint8Array(buffer);
                                    for (let i = 0; i < binaryString.length; i++) {
                                        uint8View[i] = binaryString.charCodeAt(i);
                                    }
                                    console.log({method, payloadData, buffer});
                                }
                            });
                        });
                    }
                }
            });
        }
    });

}

let isSetHeader = false;
let intervalBackground = null;
function runBackground(){
    console.log('runBackground');
    if( intervalBackground ){
        clearInterval(intervalBackground);
    }

    chrome.storage.sync.get(['isRun', 'groupId', 'isPosting'], (settings) => {
        const { isRun, groupId, isPosting } = settings;
        console.log( isRun, groupId, isPosting );

        if( !isSetHeader ){
            chrome.webRequest.onBeforeSendHeaders.addListener(
                (details) => {
                    for (let header of details.requestHeaders) {
                        if (header.name.toLowerCase() === 'user-agent') {
                            header.value = mobileUserAgent;
                        }
                    }
                    return { requestHeaders: details.requestHeaders };
                },
                { urls: ["*://*.facebook.com/*"+groupId+"*"] },
                ["blocking", "requestHeaders"]
            );
            isSetHeader = true;
        }

        if( isRun ){
            intervalBackground = setInterval( function(){
                if( !isPosting ) checkTabs( groupId );
            }, 5000);
        }
    });
}


chrome.storage.sync.set({ isPosting: false }, () => {});

runBackground();

chrome.storage.onChanged.addListener((changes, areaName) => {
    if (areaName === "sync" && !Object.keys(changes).includes('binUrl') && !Object.keys(changes).includes('isPosting') ) {
        runBackground();
    }
});
















chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    let input = message.data;

    if (message.action === "getTextbin") {
        getTextbin(input).then(result=>{
            sendResponse({success:true, result:result});
        }).catch(e=>{
            sendResponse({success:false, error:e});
        });
        return true;
    }else if (message.action === "saveTextbin") {
        saveTextbin(input).then(result=>{
            sendResponse({success:true, result:result});
        }).catch(e=>{
            sendResponse({success:false, error:e});
        });
        return true;
    }
});



getTextbin = (url)=>{
    return new Promise ((resolve,reject)=>{
        fetch(url, {
            method: "GET",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin': '*',
            }
        })
        .then(response => {
            if( response.status == 200 ){
                return response.json()
            }else{
                throw new Error(response.status)
            }
        })
        .then(data => {
            resolve( data )
        })
        .catch(error => {
            reject("[ERROR] Failed to submit.", error);
        });
    });
}
saveTextbin = (text)=>{
    return new Promise ((resolve,reject)=>{
        fetch("https://bin.mudfish.net/api/text", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                text: text,
                ttl: '0'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == 200) {
                resolve('https://bin.mudfish.net/r/' + data.tid);
            } else {
                throw new Error('Cannot save');
            }
        })
        .catch(error => {
            reject("[ERROR] Failed to submit.", error);
        });
    });
}

