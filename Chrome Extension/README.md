# Auto Post Facebook

Study for websocket scraping

Facebook data is binaray >> how to decode?
```
chrome.debugger.onEvent.addListener(function(source, method, params) {
    if (method === "Network.webSocketFrameReceived" || method === "Network.webSocketFrameSent") {
        
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
```

