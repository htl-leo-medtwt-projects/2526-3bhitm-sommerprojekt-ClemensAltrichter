

getAllWatchpartys();

function getAllWatchpartys(){
    safeFetch("../php/scrapeWatchpartys.php")

    .then(data => {
        console.log(data);

        let watchpartys = data.data;
        let s= ``;

        watchpartys.forEach(watchparty => {

            if(watchparty.chosenMovieID != null){
            s+= `
            <a href="../php/watchpartyDetail.php?watchpartyID=${watchparty.watchpartyID}">
            <div class="watchparty">
            <div class="prevPoster">
            <img src="${watchparty.poster}" alt=""movie poster>
            </div>
            <div class="prevInfo">
                <h1 class="nameBox">${watchparty.name}</h1>
                <h2 class="dateBox">${watchparty.date}</h2>
                <h2 class="titleBox">${watchparty.title}</h2>
                <div class="barcodeBox">
                    <img src="../resource/img/barcode.png" alt="">
                </div>
            </div>
        </div>
        </a>
            `;
            };

        });

        document.getElementById("contentBox").innerHTML = s;


    })
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