

getAllWatchpartys();

function getAllWatchpartys(){
    fetch("../php/scrapeWatchpartys.php")
    .then(response => response.json())
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