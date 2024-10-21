let intervalId = null;










// https://www.facebook.com/groups/457320757462175/
console.log( navigator.userAgent );

async function startCheckingPosts(keyword, commentText, commentDoneList) {
    
    function getAllPost(){
        let res = [];
        document.querySelectorAll('[role="img"][class="m nb"]').forEach( function(divImg){
            let divComment = divImg.nextSibling.querySelector('[data-action-id]');
            
            let pn = divComment.parentNode
            for(i=2;i>0;i--){
                if( pn == null ) break;
                pn = pn.parentNode;
            }
            if( pn == null ) return;
            let post = pn.querySelector('div').nextSibling.querySelector('div').querySelector('div');
            if( post == null ) return;
            res.push(post);
        });
        return res;
    }

    async function checkNewPosts() {
        console.log('checkNewPosts');
        
        getAllPost().forEach( function(post){
            let seemore = post.querySelectorAll('span');
            seemore.forEach(sm=>{
                if( sm.querySelectorAll('span').length == 0 && sm.innerText == '...see more' ){
                    sm.click();
                }
            });
        })
        
        let posted = false
        allPost = getAllPost();
        console.log(allPost);
        for( let i=0; i<allPost.length; i++ ){
            let post = allPost[i];
            let content = post.innerText;
            console.log(content);
            if (content.includes(keyword)) {
                if( !posted ){
                    posted = await commentOnPost(post);
                }
            }
        }
    }

    function simulateTyping(element, message) {
        element.value = null;
        const inputEvent = new InputEvent('input', {
            bubbles: true,
            cancelable: true,
            inputType: 'insertText',
            data: message
        });
        element.value = message;
        element.dispatchEvent(inputEvent);
    }
    function simulateClick(element) {
        element.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
        element.dispatchEvent(new MouseEvent('mouseup', { bubbles: true }));
        element.dispatchEvent(new MouseEvent('click', { bubbles: true }));
    }

    async function getStoryFbid(post){
        return '';
        post.click();

        let ntry = 0
        let story_fbid = ''
        while( story_fbid == '' ){
            ntry++;
            if( ntry > 10 ) break;
            console.log(ntry);
            if( window.location.href.includes('story_fbid') ){
                story_fbid = window.location.href.split('story_fbid=')[1].split('&')[0]
            }
            await sleep(1000);
        }
        document.querySelector('[role="button"]').click();
        return story_fbid;
    }

    async function commentOnPost(post) {
        console.log('Found divComment');
        if( commentText == '' ) return false;

        let story_fbid = await getStoryFbid(post);

        let el = await waitFor( document.querySelector('[role="img"][class="m nb"]'), 10000);
        if( el == null ) return;

        if( commentDoneList.includes(story_fbid) ) return true;

        await updateIsPosting(true);
        // post.parentNode.parentNode.parentNode.parentNode.querySelector('[role="img"][class="m nb"]').nextSibling.querySelector('div').click();
        await sleep(1000);
        // simulateTyping(document.querySelector('textarea'), commentText);
        // await sleep(15000);
        // simulateClick(document.querySelector('[class="textbox-submit-button"]'));
        await sleep(5000);

        commentDoneList.push(story_fbid);
        
        await updateCommentDoneList(commentDoneList);
        await updateBinUrl(commentDoneList);
        await updateIsPosting(false);

        return true;
    }

    return await checkNewPosts();
}

async function updateBinUrl(commentDoneList){
    return;

    let lastedUrl = await new Promise((resolve, reject) => {
        chrome.runtime.sendMessage({ action: "saveTextbin", data: commentDoneList }, (response) => {
            resolve(response);
        });
    });
    
    console.log('lastedUrl', lastedUrl);

    if( lastedUrl.success ){
        await new Promise((resolve, reject) => {
            chrome.storage.sync.set({ binUrl: lastedUrl.result }, () => {
                resolve();
            });
        });
    }

}

async function updateCommentDoneList(commentDoneList){
    await new Promise((resolve, reject) => {
        chrome.storage.sync.set({ commentDoneList }, () => {
            resolve();
        });
    });
}

async function updateIsPosting(isPosting){
    await new Promise((resolve, reject) => {
        chrome.storage.sync.set({ isPosting }, () => {
            resolve();
        });
    });
}


const sleep=(ms)=> new Promise(resolve => setTimeout(resolve, ms));
const waitFor=async(el, timeout)=>{
    const interval=100;
    let sum_interval=0
    while(el==null) {
       sum_interval+=interval;
       if (sum_interval>=timeout) break;
       await sleep(interval);
    }
    //Sempre retorna o elemento, senão existir será null
    return el;
}



async function run_app() {
    // document.querySelector('body').style.display = 'none';

    const chromStorage = ["isRun", "groupId", "reloadInterval", "keyword", "commentText", "commentDoneList", "binUrl"];
    
    // Await chrome.storage.sync.get
    const settings = await new Promise((resolve, reject) => {
        chrome.storage.sync.get(chromStorage, (result) => {
            resolve(result);
        });
    });

    if (intervalId) {
        clearInterval(intervalId);
    }

    // setTimeout(async function () {
        let { isRun, groupId, reloadInterval, keyword, commentText, commentDoneList, binUrl } = settings;
        console.log(isRun, groupId, reloadInterval, keyword, commentText, commentDoneList, binUrl);

        try {
            if (binUrl != '' && binUrl != null) {
                const response = await new Promise((resolve, reject) => {
                    chrome.runtime.sendMessage({ action: "getTextbin", data: binUrl }, (response) => {
                        resolve(response);
                    });
                });

                console.log("Background response:", response);

                if( response.success ){
                    if (JSON.stringify(response.result) !== JSON.stringify(commentDoneList)) {
                        console.log("Save commentDoneList");
                        commentDoneList = response.result;
                        await updateCommentDoneList(commentDoneList);
                    }
                }else{
                    throw 'getTextbin error';
                }
            }else{
                throw 'binUrl blank';
            }
        } catch (e) {
            console.log(e);
            // commentDoneList = [];
            // await updateCommentDoneList(commentDoneList);
            // await updateBinUrl(commentDoneList);
        }


        if (isRun && groupId && reloadInterval && keyword) {
            const currentUrl = window.location.href;
            if (currentUrl.includes(groupId)) {
                await startCheckingPosts(keyword, commentText, commentDoneList);

                intervalId = setInterval(async function () {
                    const settings = await new Promise((resolve, reject) => {
                        chrome.storage.sync.get(['commentDoneList'], (result) => {
                            resolve(result);
                        });
                    });

                    const { commentDoneList } = settings;
                    console.log('commentDoneList', commentDoneList);
                    await startCheckingPosts(keyword, commentText, commentDoneList);
                }, reloadInterval * 1000);
            }
        }

    // }, 5000);
}

function run_appxx(){
    document.querySelector('body').style.display = 'none';
    
    const chromStorage = ["isRun", "groupId", "reloadInterval", "keyword", "commentText", "commentDoneList", "binUrl"];
    
    chrome.storage.sync.get(chromStorage, (settings) => {
        setTimeout( async function(){
            const { isRun, groupId, reloadInterval, keyword, commentText, commentDoneList, binUrl } = settings;
            console.log( isRun, groupId, reloadInterval, keyword, commentText, commentDoneList, binUrl );

            try{
                if( settings.binUrl ){
                    chrome.runtime.sendMessage({ action: "getTextbin", data: settings.binUrl }, function(response) {
                        if( response.success ){
                            console.log("Background response:", response.result);
                            
                            if( JSON.stringify(response.result) != JSON.stringify(commentDoneList) ){
                                console.log("Save commentDoneList");
                                updateCommentDoneList(response.result)
                            }
                        }else{
                            throw new Error(response.error);
                        }
                    });
                }
            }catch(e){
                console.log(e);
                alert('Textbin url is error');
                return;
            }

            if (intervalId) {
                clearInterval(intervalId);
            }

            if (isRun && groupId && reloadInterval && keyword) {
                const currentUrl = window.location.href;
                if( currentUrl.includes(groupId) ){
                    await startCheckingPosts(keyword, commentText, commentDoneList);

                    intervalId = setInterval(async function(){
                        chrome.storage.sync.get(['commentDoneList'], async (settings) => {
                            const { commentDoneList } = settings;
                            console.log(commentDoneList);
                            await startCheckingPosts(keyword, commentText, commentDoneList);
                        });
                    }, reloadInterval * 1000);
                }
            }
        }, 5000);
    });
}











chrome.storage.sync.set({ isPosting: false }, () => {});

run_app();

chrome.storage.onChanged.addListener((changes, areaName) => {
    if (areaName === "sync" && !Object.keys(changes).includes('binUrl') && !Object.keys(changes).includes('isPosting') ) {
        run_app();
    }
});


