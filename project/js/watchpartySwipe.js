/* ═══════════════════════════════════════════
   watchpartySwipe.js
═══════════════════════════════════════════ */

const params      = new URLSearchParams(window.location.search);
const watchpartyID = params.get('partyID');

let movies       = [];
let currentIndex = 0;
let pollInterval = null;

// ── START ─────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadMovies();
});

// ── FILME LADEN ───────────────────────────
function loadMovies() {
    safeFetch(`../php/scrapeSwipe.php?getMovies=${watchpartyID}`)

        .then(data => {
            if (data.code !== 200 || data.data.length === 0) return;
            movies = data.data;
            renderCard();
        });
}

// ── KARTE ANZEIGEN ────────────────────────
function renderCard() {
    if (currentIndex >= movies.length) {
        showWaiting();
        return;
    }

    const m = movies[currentIndex];
    const card = document.getElementById('swipeCard');

    // Reset Animation
    card.classList.remove('fly-right', 'fly-left');
    card.style.transition = 'none';
    card.style.transform  = '';
    card.style.opacity    = '1';

    setTimeout(() => {
        card.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
    }, 10);

    document.getElementById('cardImg').src       = m.poster || '';
    document.getElementById('cardTitle').textContent    = m.title;
    document.getElementById('cardOverview').textContent = m.overview;
    document.getElementById('cardRating').textContent   = m.voteAVG ? '★ ' + parseFloat(m.voteAVG).toFixed(1) : '';
    document.getElementById('swipeCounter').textContent = `${currentIndex + 1} / ${movies.length}`;
}

// ── SWIPEN ────────────────────────────────
function swipe(liked) {
    if (currentIndex >= movies.length) return;

    const movie = movies[currentIndex];
    const card  = document.getElementById('swipeCard');

    // Animation
    card.classList.add(liked ? 'fly-right' : 'fly-left');

    // Swipe in DB speichern
    safeFetch(`../php/scrapeSwipe.php?swipe=1&watchpartyID=${watchpartyID}&movieID=${movie.movieID}&liked=${liked}`)


    // Nächste Karte nach Animation
    setTimeout(() => {
        currentIndex++;
        renderCard();
    }, 280);
}

// ── WARTEN BIS ALLE FERTIG ────────────────
function showWaiting() {
    document.getElementById('swipeScreen').style.display  = 'none';
    document.getElementById('waitingScreen').style.display = 'block';

    pollInterval = setInterval(checkDone, 3000);
    checkDone();
}

function checkDone() {
    safeFetch(`../php/scrapeSwipe.php?checkDone=${watchpartyID}`)

        .then(data => {
            if (data.code !== 200) return;

            const { allDone, doneMemebers, totalMembers } = data.data;

            document.getElementById('doneStatus').textContent =
                `${doneMemebers} von ${totalMembers} fertig`;

            if (allDone) {
                clearInterval(pollInterval);
                showResult();
            }
        });
}

// ── ERGEBNIS ──────────────────────────────
function showResult() {
    safeFetch(`../php/scrapeSwipe.php?getResult=${watchpartyID}`)

        .then(data => {
            if (data.code !== 200) return;

            document.getElementById('waitingScreen').style.display = 'none';
            document.getElementById('resultScreen').style.display  = 'block';

            const movies = data.data;
            if (movies.length === 0) return;

            // Winner
            const winner = movies[0];
            document.getElementById('winnerCard').innerHTML = `
                <img src="${winner.poster}" alt="${winner.title}" style="width:100%;aspect-ratio:2/3;object-fit:cover;display:block;">
                <div style="padding:14px 16px;">
                    <div class="card-title">${winner.title}</div>
                    <div class="result-likes">♥ ${winner.likes} Likes</div>
                </div>
            `;

            // Alle Filme
            document.getElementById('allResults').innerHTML = movies.map(m => `
                <div class="result-item">
                    <img class="result-poster" src="${m.poster}" alt="${m.title}">
                    <div class="result-info">
                        <div class="result-title">${m.title}</div>
                        <div class="result-likes">♥ ${m.likes} Likes</div>
                    </div>
                </div>
            `).join('');
        });
}
