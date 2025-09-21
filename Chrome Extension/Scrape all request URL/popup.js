document.getElementById('start').addEventListener('click', async () => {
  // get active tab
  const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
  if (!tab) return alert('No active tab found');
  chrome.runtime.sendMessage({ type: 'setTarget', tabId: tab.id }, resp => {
    document.getElementById('out').textContent = 'Started capturing on tabId: ' + resp.targetTabId;
  });
});

document.getElementById('stop').addEventListener('click', () => {
  chrome.runtime.sendMessage({ type: 'stop' }, () => {
    document.getElementById('out').textContent = 'Stopped';
  });
});

document.getElementById('refresh').addEventListener('click', () => {
  chrome.runtime.sendMessage({ type: 'getCollected' }, (resp) => {
    const data = resp.collected || [];
    document.getElementById('out').textContent = JSON.stringify(data.slice(-200), null, 2);
  });
});
