// This code is not working

(function() {
  const origOpen = XMLHttpRequest.prototype.open;
  const origSend = XMLHttpRequest.prototype.send;

  XMLHttpRequest.prototype.open = function(method, url) {
    this._url = url; // store URL
    return origOpen.apply(this, arguments);
  };

  XMLHttpRequest.prototype.send = function(body) {
    this.addEventListener('load', () => {
      // Example filter: only capture responses with #EXT-X-ENDLIST
      if (this.responseText.includes('#EXT-X-ENDLIST')) {
        chrome.runtime.sendMessage({
          type: 'foundURL',
          pageURL: window.location.href,
          xhrURL: this._url
        });
      }
    });
    return origSend.apply(this, arguments);
  };
})();
