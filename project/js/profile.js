async function api(data) {
    const form = new FormData();
    Object.entries(data).forEach(([k, v]) => form.append(k, v));
    const res = await fetch('../userSys/auth.php', { method: 'POST', body: form });
    return res.json();
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

    fetch(`../php/scrapeWatchpartys.php?acceptInvite=${partyID}`)
        .then(r => r.json())
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

    fetch(`../php/Profile.php?rejectInvite=${notificationID}`)
        .then(r => r.json())
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