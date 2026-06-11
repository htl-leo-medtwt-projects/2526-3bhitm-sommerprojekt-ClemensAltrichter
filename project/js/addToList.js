/* ═══════════════════════════════════════════
   addToList.js — Popup zum Hinzufügen zu Listen
   Vor </body> einbinden
═══════════════════════════════════════════ */

// Popup HTML einmalig in den Body einfügen
document.body.insertAdjacentHTML('beforeend', `
  <div class="popup-overlay" id="listPopup" onclick="closeListPopup(event)">
    <div class="popup-box">
      <div class="popup-header">
        <div>
          <div class="popup-title" id="popupMovieTitle">Film hinzufügen</div>
          <div class="popup-subtitle">Wähle eine Liste</div>
        </div>
        <button class="popup-close" onclick="closeListPopup()">✕</button>
      </div>
      <div class="popup-lists" id="popupLists">
        <!-- Listen werden per PHP/JS befüllt -->
      </div>
    </div>
  </div>
`);

let currentMovieID = null

// Popup öffnen
function openListPopup(movieID, movieTitle) {
  ;
  currentMovieID = movieID;

  document.getElementById('popupMovieTitle').textContent = movieTitle;
  document.getElementById('listPopup').classList.add('visible');

  loadUserLists(movieID);
}

// Popup schließen (nur bei Klick auf Overlay, nicht auf Box)
function closeListPopup(event) {
  if (!event || event.target === document.getElementById('listPopup')) {
    document.getElementById('listPopup').classList.remove('visible');
    currentMovieID = null;
  }
}

// Listen vom Server laden und anzeigen
async function loadUserLists(movieID) {
  const container = document.getElementById('popupLists');
  container.innerHTML = '<div style="color:var(--greyscale-color);font-family:miriam,sans-serif;font-size:0.9rem;padding:10px 0;">Laden...</div>';

  let lists= await getAllLists();

  console.log(lists);
  container.innerHTML = '';

  if(lists.data.length === 0){
        container.innerHTML = '<div style="color:var(--greyscale-color);font-family:miriam,sans-serif;font-size:0.9rem;padding:10px 0;">Keine Listen vorhanden.</div>';
        return;
  }

  let s = '';

  lists.data.forEach(list => {
    s+= `
    <div class="list-option ${list.alreadyAdded ? 'added' : ''}"
             onclick="addToList(${list.listID}, this)">
          <span class="list-option-name">${list.name}</span>
          <span class="list-option-count">${list.alreadyAdded ? 'bereits drin' : 'add'}</span>
        </div>
    `;


  })

  document.getElementById('popupLists').innerHTML = s;

  /*

  const form = new FormData();
  form.append('action', 'getLists');
  form.append('movieID', movieID);

  fetch('../php/listActions.php', { method: 'POST', body: form })
    .then(r => r.json())
    .then(data => {
      if (!data.lists || data.lists.length === 0) {
        container.innerHTML = '<div style="color:var(--greyscale-color);font-family:miriam,sans-serif;font-size:0.9rem;padding:10px 0;">Keine Listen vorhanden.</div>';
        return;
      }

      container.innerHTML = data.lists.map(list => `
        <div class="list-option ${list.alreadyAdded ? 'added' : ''}"
             onclick="addToList(${list.listID}, this)">
          <span class="list-option-name">${list.name}</span>
          <span class="list-option-count">${list.alreadyAdded ? 'bereits drin' : list.movieCount + ' Filme'}</span>
        </div>
      `).join('');
    })
    .catch(() => {
      container.innerHTML = '<div style="color:var(--accent-color);font-family:miriam,sans-serif;font-size:0.9rem;">Fehler beim Laden.</div>';
    });

    */


}


async function getAllLists(){
    console.log("fetching lists...");
    /*
    fetch('../php/scrapeLists.php')
    .then(response => response.json())
    .then(data => {
        console.log(data);
        
        return data;
    })
        */

    const response = await safeFetch('../php/scrapeLists.php');
    const data = await response.json();
    console.log(data);
    return data;
}

// Film zur Liste hinzufügen
/*
function addToList(listID, element) {
  if (element.classList.contains('added')) return; // schon drin

  const form = new FormData();
  form.append('action', 'addMovie');
  form.append('listID', listID);
  form.append('movieID', currentMovieID);

  fetch('../php/listActions.php', { method: 'POST', body: form })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        element.classList.add('added');
        element.querySelector('.list-option-count').textContent = '✓ bereits drin';
      }
    });
}
*/

function addToList(listID, element) {
  if (element.classList.contains('added')) return;

  safeFetch(`../php/scrapeLists.php?addMovie=${currentMovieID}&listID=${listID}`)
    .then(r => r.json())
    .then(data => {
      if (data.code === 200) {
        element.classList.add('added');
        element.querySelector('.list-option-count').textContent = '✓ bereits drin';
      }
    });
}