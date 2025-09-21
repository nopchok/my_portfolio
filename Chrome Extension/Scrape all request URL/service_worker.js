let targetTabId = null;
let collected = []; // keep in-memory; also mirror to storage

// helper: save into chrome.storage so popup can read later
async function persist() {
  await chrome.storage.local.set({ collected });
}

// receive messages from popup (start/stop/get)
chrome.runtime.onMessage.addListener((msg, sender, sendResp) => {
  if (msg.type === 'setTarget') {
    targetTabId = msg.tabId;
    collected = [];
    persist();
    sendResp({ ok: true, targetTabId });
  } else if (msg.type === 'stop') {
    targetTabId = null;
    sendResp({ ok: true });
  } else if (msg.type === 'getCollected') {
    sendResp({ collected });
  } else if (msg.type === "getIsActiveTab") {
    sendResp({ isActiveTab: targetTabId == sender.tab.id });
  } else if (msg.type === "foundURL") {
    console.log( 'foundURL', msg.foundURL );
  }
  // return true if async; not needed here
});

// listen to web requests and collect those from the target tab
chrome.webRequest.onBeforeRequest.addListener(
  (details) => {
    // details.tabId is -1 for non-tab requests (e.g., browser internal)
    if (targetTabId !== null && details.tabId === targetTabId) {
      // record a compact object
      const entry = {
        // time: new Date(details.timeStamp).toISOString(),
        method: details.method,
        url: details.url,
        // requestId: details.requestId,
        type: details.type,
        // tabId: details.tabId
      };
      collected.push(entry);

      // optionally persist incrementally
      if (collected.length % 20 === 0) persist();
    }
  },
  { urls: ["<all_urls>"] } // must match hosts; host_permissions above
);

chrome.action.onClicked.addListener((tab) => {
    // IMPORTANT: everything stays inside this handler
    chrome.sidePanel.setOptions({
      tabId: tab.id,
      path: "popup.html",
      enabled: true
    }, () => {
      // open panel directly here (still inside user gesture)
      chrome.sidePanel.open({ tabId: tab.id });
    });
  });  