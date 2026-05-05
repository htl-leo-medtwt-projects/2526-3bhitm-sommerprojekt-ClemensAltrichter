let lists = [];
let friends = [];

let selectedLists = [];
let selectedFriends = [];

// LISTS LADEN
fetch("../php/watchpartyConf copy.php?getLists=true")
.then(response => response.json())
.then(data => {

    lists = data;
    displayAllLists();

});

// FRIENDS LADEN
fetch("../php/watchpartyConf copy.php?getFriends=true")
.then(response => response.json())
.then(data => {

    friends = data;
    display4Friends();

});

// FRIENDS ANZEIGEN
function display4Friends(){

    let s= '';

    if(friends.length < 1){
        s = "<p id='noFriends'>No friends to invite yet</p>";
    }else{

        for(let i = 0; i < Math.min(4,friends.length); i++){
            s+= "<div class='userBox'>";
            s+= "<img class='avatar' src='../resource/img/" + friends[i]['avatar'] + "'>";
            s+= "<h2>" + friends[i]['username'] + "</h2>";
            s+= `<div class='inviteBox' onclick='addFriendToWatchparty(${friends[i]['userID']}, this)'></div>`;
            s+= "</div>";
        }
    }

    document.getElementById("friendContainer").innerHTML = s;
}

// LISTS ANZEIGEN
function displayAllLists(){
    let s = "";

    for(let i = 0; i < lists.length; i++){
        s += "<div class='listBox'>";
        s += "<h2>" + lists[i]['name'] + "</h2>";
        s += `<div class='listCheckBox' onclick='addListToWatchparty(${lists[i]['listID']}, this)'></div>`;
        s += "</div>";
    }

    document.getElementById("listBox").innerHTML = s;
}

// LISTS AUSWÄHLEN
function addListToWatchparty(listID, ob){

    let found = false;

    for(let i = 0; i < selectedLists.length; i++){
        if(selectedLists[i] == listID){
            ob.style.backgroundColor = "var(--primary-color)";
            selectedLists.splice(i, 1);
            found = true;
        }
    }

    if(!found){
        ob.style.backgroundColor = "var(--secondary-color)";
        selectedLists.push(listID);
    }

    console.log(selectedLists);
}

// FRIENDS AUSWÄHLEN
function addFriendToWatchparty(userID, ob){

    let found = false;

    for(let i = 0; i < selectedFriends.length; i++){
        if(selectedFriends[i] == userID){
            ob.style.backgroundColor = "var(--primary-color)";
            selectedFriends.splice(i, 1);
            found = true;
        }
    }

    if(!found){
        ob.style.backgroundColor = "var(--secondary-color)";
        selectedFriends.push(userID);
    }

    console.log(selectedFriends);
}

// SUBMIT
async function submitWatchparty(){

    const name = document.getElementById("nameInput").value.trim();
    const location = document.getElementById("locationInput").value.trim();

    if(name === ""){
        alert("Bitte Name eingeben");
        return;
    }

    if(selectedLists.length === 0){
        alert("Bitte mindestens eine Liste auswählen");
        return;
    }

    try{

        const res = await fetch("../php/watchpartyConf copy.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                name: name,
                location: location,
                lists: selectedLists,
                friends: selectedFriends
            })
        });

        const data = await res.json();

        if(data.success){
            alert("Watchparty erstellt!");
            window.location.href = "watchpartys.html";
        }else{
            alert(data.message);
        }

    }catch(err){
        console.error(err);
        alert("Serverfehler");
    }
}