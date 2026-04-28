let lists = [];
let friends = [];
let selectedLists = [];



fetch("../php/watchpartyConf.php?getLists=true")
.then(response => response.json())
.then(data => {


console.log(data);

for(let i = 0; i < data.length; i++){
    lists[i] = data[i];
}

console.log(lists);
displayAllLists();

});

fetch("../php/watchpartyConf.php?getFriends=true")
.then(response => response.json())
.then(data => {


console.log(data);

for(let i = 0; i < data.length; i++){
    friends[i] = data[i];
}

console.log(friends);

display4Friends();

});



function display4Friends(){

    let s= '';

    if(friends.length < 1){
    s = "<p id='noFriends'>No friends to invite yet</p>";
   }else{

    for(let i = 0; i < Math.min(4,friends.length); i++){
        s+= "<div class='userBox'>";
        s+= "<img class='avatar' src='../resource/img/" + friends[i]['avatar'] + "' alt='Poster'>";
        s+= "<h2>" + friends[i]['username'] + "</h2>";
        s+= "<div class='inviteBox'></div>";
        s+= "</div>";
    }
}

document.getElementById("friendContainer").innerHTML = s;
}


function displayAllLists(){
    let s = "";

    for(let i = 0; i < lists.length; i++){
        s += "<div class='listBox'>";
        s += "<h2>" + lists[i]['name'] + "</h2>";
        s += `<div class='listCheckBox'  onclick='addListToWatchparty( ${lists[i]['listID'] }, this )'></div>`;
        s += "</div>";
    }

    document.getElementById("listBox").innerHTML = s;
}





function addListToWatchparty(listID, ob){
//ob.style.backgroundColor = "var(--secondary-color)";
let found = false;

for(let i = 0; i < selectedLists.length; i++){
    if(selectedLists[i] == listID){
        ob.style.backgroundColor = "var(--primary-color)";
        found = true;
        selectedLists.splice(i, 1);
    }
    }

    if(!found){
        ob.style.backgroundColor = "var(--secondary-color)";
        selectedLists.push(listID);
    }


console.log(listID);
console.log(selectedLists);
}