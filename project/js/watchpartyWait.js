const params  = new URLSearchParams(window.location.search);
const partyID = params.get('partyID');
let sessionUserID = null;
let pollInterval;

function startPolling() {
  // zuerst Session-UserID holen, dann Polling starten
  fetch('../userSys/auth.php', {
    method: 'POST',
    body: new URLSearchParams({ action: 'check' })
  })
    .then(r => r.json())
    .then(data => {
      if (!data.loggedIn) {
        window.location.href = '../userSys/index.html';
        return;
      }
      sessionUserID = data.userID; // userID aus Session
      pollInterval = setInterval(pollStatus, 3000);
      pollStatus();
    });
}

function pollStatus() {
  fetch(`../php/scrapeWatchpartys.php?getPartyStatus=${partyID}`)
    .then(r => r.json())
    .then(data => {
      if (data.code !== 200) return;

      const { status, members, partyName } = data.data;

      // Party Name anzeigen
      document.getElementById('partyName').textContent = partyName || '';

      renderMembers(members);

      if (status === 'active') {
        clearInterval(pollInterval);
        window.location.href = `watchpartySwipe.html?partyID=${partyID}`;
      }
    });
}

function renderMembers(members) {
  const container = document.getElementById('memberList');
  container.innerHTML = members.map(m => `
    <div class="friend-item">
        <div class="friend-avatar">${m.avatar}</div>
        <span class="friend-name">${m.username}</span>
        <span class="ready-badge" style="color: ${m.memberStatus === 'joined' ? '#50c878' : 'var(--greyscale-color)'};">
            ${m.memberStatus === 'joined' ? '● ready' : '● pending'}
        </span>
    </div>
`).join('');

  const isHost = members.some(m => m.isHost == 1 && m.userID == sessionUserID);
  document.getElementById('startRow').style.display = isHost ? 'flex' : 'none';
  document.getElementById('waitingMsg').style.display = isHost ? 'none' : 'block';
}

function startParty() {
  fetch(`../php/scrapeWatchpartys.php?startParty=${partyID}`)
    .then(r => r.json())
    .then(data => {
      if (data.code === 200) {
        // Polling erkennt den Wechsel automatisch
      }
    });
}

document.addEventListener('DOMContentLoaded', startPolling);