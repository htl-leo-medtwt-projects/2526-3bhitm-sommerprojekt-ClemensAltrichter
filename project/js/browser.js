/* ═══════════════════════════════════════════
   nav.js — Aufklappbare Top Navigation
═══════════════════════════════════════════ */
let movies = []; 
let currentIndex = 0;
let numOfMoviesToShow = 6; 

getAllMovies();


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



function getAllMovies(){
    fetch('../php/scrapeMovies.php')
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