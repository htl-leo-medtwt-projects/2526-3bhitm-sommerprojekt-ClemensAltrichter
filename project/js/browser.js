/* ═══════════════════════════════════════════
   nav.js — Aufklappbare Top Navigation
═══════════════════════════════════════════ */
let movies = []; 
let currentIndex = 0;
let numOfMoviesToShow = 6; 




document.addEventListener('DOMContentLoaded', () => {
    getAllMovies();

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



function getAllMovies(){
    safeFetch('../php/scrapeMovies.php') //..............
    .then(response => response.json())
    .then(data => {

     movies = data.data;
        console.log(movies);
        showNext();

    })
}

function showNext(){
  console.log("showing next movies");
  console.log(currentIndex);
  console.log(movies.length);
  
  
let s = "";

for(let i = currentIndex; i < currentIndex + numOfMoviesToShow; i++){
  console.log(i);
    if(i >= movies.length) break;
    s += `
    <div class="movieCard" i="${movies[i].movieID}">
        <div class="posterContainer">
            <img src="${movies[i].poster}" alt="${movies[i].title} poster">
            <button class="addToListBtn" onclick="openListPopup(${movies[i].movieID}, '${movies[i].title}')">+</button>
        </div>
        <div class="titleContainer">${movies[i].title}</div>
        <div class="descContainer">${movies[i].overview}</div>
    </div>
    `;
    
}
currentIndex+= numOfMoviesToShow;


document.getElementById("discoverContainer").innerHTML += s;

}


let searchTimeout = null;
let isSearching = false;

function handleSearch(query) {
    const clearBtn = document.getElementById('clearSearch');
    clearBtn.style.display = query.length > 0 ? 'block' : 'none';

    // Debounce — erst nach 400ms suchen damit nicht bei jedem Buchstaben ein Request geht
    clearTimeout(searchTimeout);
    if (query.length < 2) {
        if (!isSearching) return;
        clearSearch();
        return;
    }

    searchTimeout = setTimeout(() => {
        searchMovies(query);
    }, 400);
}

function searchMovies(query) {
    isSearching = true;

    safeFetch(`../php/scrapeMovies.php?search=${encodeURIComponent(query)}`)
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('discoverContainer');
            document.getElementById('buttonContainer').style.display = 'none';

            if (!data.data || data.data.length === 0) {
                container.innerHTML = '<p class="noResults">No movies found for "' + query + '"</p>';
                return;
            }

            container.innerHTML = data.data.map(m => `
                <div class="movieCard" i="${m.movieID}">
                    <div class="posterContainer">
                        <img src="${m.poster}" alt="${m.title} poster">
                        <button class="addToListBtn" onclick="openListPopup(${m.movieID}, '${m.title}')">+</button>
                    </div>
                    <div class="titleContainer">${m.title}</div>
                    <div class="descContainer">${m.overview}</div>
                </div>
            `).join('');
        });
}

function clearSearch() {
    isSearching = false;
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearch').style.display = 'none';
    document.getElementById('discoverContainer').innerHTML = '';
    document.getElementById('buttonContainer').style.display = 'flex';
    currentIndex = 0;
    showNext();
}