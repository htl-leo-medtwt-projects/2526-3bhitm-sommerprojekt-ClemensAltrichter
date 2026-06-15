async function api(data) {
    const form = new FormData();
    Object.entries(data).forEach(([k, v]) => form.append(k, v));
    const datas = await safeFetch('../userSys/auth.php', { method: 'POST', body: form });
    return datas;
}

async function logout() {
    await api({ action: 'logout' });
    window.location.href = '../userSys/index.html';
}

/*
function acceptInvite(partyID) {
    fetch(`../php/scrapeWatchpartys.php?acceptInvite=${partyID}`)
        .then(r => r.json())
        .then(data => {
            if (data.code === 200) {
                window.location.href = `../pages/watchpartyWait.html?partyID=${partyID}`;
            }
        });
}
*/
/*
function acceptInvite(btn) {
    const box = btn.closest('.notificationBox');
    const partyID = box.dataset.partyid;

    fetch(`../php/scrapeWatchpartys.php?acceptInvite=${partyID}`)
        .then(r => r.json())
        .then(data => {
            if (data.code === 200) {
                window.location.href = `../pages/watchpartyWait.html?partyID=${partyID}`;
            }
        });
}*/
function acceptInvite(btn) {
    const box = btn.closest('.notificationBox');
    const partyID = box.dataset.partyid;

    safeFetch(`../php/scrapeWatchpartys.php?acceptInvite=${partyID}`)
        .then(data => {
            if (data.code === 200) {
                window.location.href = `../pages/watchpartyWait.html?partyID=${partyID}`;
            }
        });
}

/*
function rejectInvite(partyID, notificationBox) {
    fetch(`../php/Profile.php?rejectInvite=${partyID}`)
        .then(r => r.json())
        .then(data => {
            if (data.code === 200) {
                // Box aus dem DOM entfernen ohne Seite neu zu laden
                notificationBox.style.opacity = '0';
                notificationBox.style.transition = 'opacity 0.3s ease';
                setTimeout(() => notificationBox.remove(), 300);
            }
        });
}
*/
/*
function rejectInvite(btn) {
    const box = btn.closest('.notificationBox');
    console.log('box:', box);
    console.log('partyid:', box?.dataset?.partyid);
    const partyID = box.dataset.partyid;

    fetch(`../php/Profile.php?rejectInvite=${partyID}`)
        .then(r => r.json())
        .then(data => {
            if (data.code === 200) {
                box.style.opacity = '0';
                box.style.transition = 'opacity 0.3s ease';
                setTimeout(() => box.remove(), 300);
            }
        });
}*/
function rejectInvite(btn) {
    const box = btn.closest('.notificationBox');
    const notificationID = box.dataset.notificationid;

    safeFetch(`../php/Profile.php?rejectInvite=${notificationID}`)

        .then(data => {
            if (data.code === 200) {
                box.style.opacity = '0';
                box.style.transition = 'opacity 0.3s ease';
                setTimeout(() => box.remove(), 300);
            }
        });
}


function deleteAccount() {
    if (!confirm('Account wirklich löschen? Das kann nicht rückgängig gemacht werden.')) return;
    window.location.href = '../php/Profile.php?deleteUser=true';
}

let searchTimeout = null;

function handleUserSearch(query) {
    clearTimeout(searchTimeout);
    const results = document.getElementById('searchResults');

    if (query.length < 2) {
        results.innerHTML = '';
        return;
    }

    searchTimeout = setTimeout(() => {
        safeFetch(`../php/Profile.php?searchUser=${encodeURIComponent(query)}`)

            .then(data => {
                if (!data.data || data.data.length === 0) {
                    results.innerHTML = '<p class="noResults">No users found</p>';
                    return;
                }
                results.innerHTML = data.data.map(u => `
                    <div class="userSearchResult" data-userid="${u.userID}">
                        <span class="searchUsername">${u.username}</span>
                        <button class="addFriendBtn" onclick="sendFriendRequest(${u.userID}, this)">+</button>
                    </div>
                `).join('');
            });
    }, 400);
}

function sendFriendRequest(toUserID, btn) {
    safeFetch(`../php/Profile.php?sendRequest=${toUserID}`)

        .then(data => {
            if (data.code === 200) {
                btn.textContent = '✓';
                btn.disabled = true;
                btn.style.borderColor = '#50c878';
                btn.style.color = '#50c878';
            } else {
                btn.textContent = '!';
            }
        });
}

function acceptFriendRequest(btn) {
    const box = btn.closest('.notificationBox');
    const fromUserID = box.dataset.fromuserid;
    const notificationID = box.dataset.notificationid;

    safeFetch(`../php/Profile.php?acceptRequest=${fromUserID}&notificationID=${notificationID}`)

        .then(data => {
            if (data.code === 200) {
                box.style.opacity = '0';
                box.style.transition = 'opacity 0.3s ease';
                setTimeout(() => box.remove(), 300);
            }
        });
}


// NAV
document.addEventListener('DOMContentLoaded', () => {

  const nav    = document.querySelector('.top-nav');
  const header = document.querySelector('.nav-header');

  if (!nav || !header) return;

  // Auf/Zu beim Klick auf Header
  header.addEventListener('click', () => {
    nav.classList.toggle('open');
  });

  // Schließen bei Klick außerhalb
  document.addEventListener('click', (e) => {
    if (!nav.contains(e.target)) {
      nav.classList.remove('open');
    }
  });

  // Schließen beim Navigieren
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
    });
  });

});