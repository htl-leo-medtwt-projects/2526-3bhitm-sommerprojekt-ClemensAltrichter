const listContainer = document.getElementById('contentBox');

getAllLists();
function getAllLists(){
    console.log("fetching lists...");
    fetch('../php/scrapeLists.php')
    .then(response => response.json())
    .then(data => {
        console.log(data);
        
        listContainer.innerHTML = ''; // Clear existing content
        let s = '';

        data.data.forEach(list => {
            console.log(list);
             s += `
            <div class="list" id="${list.id}">
            <img src="../resource/img/eye_icon.png" alt="eye icon" class="eyeIcon">
            <h1>${list.name}</h1>
            </div>
            `;
        })
        listContainer.innerHTML = s;
    })
}