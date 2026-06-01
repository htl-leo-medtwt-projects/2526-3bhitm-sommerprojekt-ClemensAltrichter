async function api(data) {
  const form = new FormData();
  Object.entries(data).forEach(([k, v]) => form.append(k, v));
  const res = await fetch('../userSys/auth.php', { method: 'POST', body: form });
  return res.json();
}
async function logout() {
  await api({ action: 'logout' });
  console.log('Logged out');
    window.location.href = '../userSys/index.html';
}

function acceptInvite(partyID) {
  fetch(`../php/scrapeWatchpartys.php?acceptInvite=${partyID}`)
    .then(r => r.json())
    .then(data => {
      if (data.code === 200) {
        window.location.href = `../pages/watchpartyWait.html?partyID=${partyID}`;
      }
    });
}