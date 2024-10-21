document.getElementById("saveDev").addEventListener("click", () => {
    const binUrl = document.getElementById("binUrl").value;
    
    chrome.storage.sync.set({ binUrl }, () => {});
});

document.getElementById("clearComment").addEventListener("click", () => {
    commentDoneList = [];
    chrome.storage.sync.set({ commentDoneList }, ()=>{});
});



document.getElementById("save").addEventListener("click", () => {
    const isRun = document.getElementById("isRun").checked;
    const groupId = document.getElementById("group_id").value;
    const reloadInterval = parseInt(document.getElementById("interval").value, 10);
    const keyword = document.getElementById("keyword").value;
    const commentText = document.getElementById("comment").value;

    chrome.storage.sync.set({ isRun, groupId, reloadInterval, keyword, commentText }, () => {
        // alert("Settings saved successfully!");
    });
    
    /*
    if (groupId && reloadInterval > 0 && keyword ) {
        chrome.storage.sync.set({ isRun, groupId, reloadInterval, keyword, commentText }, () => {
            // alert("Settings saved successfully!");
        });
    } else {
        // alert("Please fill out all fields.");
    }
    */
});



document.addEventListener("DOMContentLoaded", () => {
    chrome.storage.sync.get(["isRun", "groupId", "reloadInterval", "keyword", "commentText", "binUrl"], async (settings) => {
        if (settings.isRun) {
            document.getElementById("isRun").checked = settings.isRun;
        }
        if (settings.groupId) {
            document.getElementById("group_id").value = settings.groupId;
        }
        if (settings.reloadInterval) {
            document.getElementById("interval").value = settings.reloadInterval;
        }
        if (settings.keyword) {
            document.getElementById("keyword").value = settings.keyword;
        }
        if (settings.commentText) {
            document.getElementById("comment").value = settings.commentText;
        }
        if (settings.binUrl) {
            document.getElementById("binUrl").value = settings.binUrl;
        }
    });
});



getTextbin = (url)=>{
    return new Promise ((resolve,reject)=>{
        fetch(url, {
            method: "GET",
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if( response.status == 200 ){
                return response.text()
            }else{
                throw new Error(response.status)
            }
        })
        .then(data => {
            resolve(data)
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
                resolve(data.tid);
            } else {
                throw new Error('Cannot save');
            }
        })
        .catch(error => {
            reject("[ERROR] Failed to submit.", error);
        });
    });
}

document.getElementById("pasteBtn").addEventListener("click", function() {
    let _this = this
    navigator.clipboard.readText().then(function(clipboardText) {
        _this.value = clipboardText;
    }).catch(function(error) {
        console.error('Failed to read clipboard content: ', error);
    });
});

// var t = document.createElement("input");
// t.setAttribute('id', 'pasteBtn');
// document.body.appendChild(t);
// console.log(8888)

// document.querySelector('[id="pasteBtn"]').focus();
// document.querySelector('[id="pasteBtn"]').addEventListener("click", function() {
//     let _this = this
//     navigator.clipboard.readText().then(function(clipboardText) {
//         console.log(clipboardText);
//         _this.value = clipboardText;
//     }).catch(function(error) {
//         console.error('Failed to read clipboard content: ', error);
//     });
// });

// console.log(9999)
// t.click();
// console.log( t.value );
// document.body.removeChild(t);