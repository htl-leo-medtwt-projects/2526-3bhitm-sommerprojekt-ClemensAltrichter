const listContainer = document.getElementById('contentBox');
const formBox = document.getElementById('formBox');

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
             <a href="../php/listDetail.php?listID=${list.listID}">
            <div class="list" id="list${list.listID}">
            <img src="../resource/img/eye_icon.png" alt="eye icon" class="eyeIcon">
            <h1>${list.name}</h1>
            </div>
            </a>
            `;
        })
        listContainer.innerHTML = s;
    })
}

function displayNewListForm(){

    $s= `
    <h1>new List</h1>
    
    <form action="../php/listConf.php" method="POST">
            <input type="text" name="listName" id="listName" placeholder="List Name">
            

        <button type="submit">
        <img src="../resource/img/check_icon light.png" alt="check icon">
        </button>
        </form>

        <div id="cancelBTN" onclick="formBox.style.display='none'">
            cancel
        </div>
        
        `;

    formBox.innerHTML = $s;

    formBox.style.display = 'block';





}