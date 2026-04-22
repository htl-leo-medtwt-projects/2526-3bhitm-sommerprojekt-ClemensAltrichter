<?php
require_once "./dbConnection.php";



 $query = "SELECT * FROM user where userID IN 
 (SELECT DISTINCT userID1 from friend where userID2 = 1
 UNION ALL
  SELECT DISTINCT userID2 from friend where userID1 =1)"; // hier muss die userID der eingeloggten Person rein

        if($conn -> query($query)){
            $result = $conn -> query($query);
            $users = $result -> fetch_all(MYSQLI_ASSOC);
        }

$query = "SELECT * FROM list where userID = 1"; // hier muss die userID der eingeloggten Person rein

        if($conn -> query($query)){
            $result = $conn -> query($query);
            $lists = $result -> fetch_all(MYSQLI_ASSOC);
        }
   


    






function display4Users(){
   // $friends = getUsers();
   global $users;

   if(count($users) < 4){
    echo "<p id='noFriends'>No friends to invite yet</p>";
   }



    for($i = 0; $i < 4; $i++){
        echo "<div class='userBox'>";
        echo "<img class='avatar' src='../resource/img/" . $users[$i]['avatar'] . "' alt='Poster'>";
        echo "<h2>" . $users[$i]['username'] . "</h2>";
        echo "<div class='inviteBox'></div>";
        echo "</div>";
    }

}

function displayAllLists(){
    global $lists;

    for($i = 0; $i < count($lists); $i++){
        echo "<div class='listBox'>";
        echo "<h2>" . $lists[$i]['name'] . "</h2>";
        echo "<div class='listCheckBox'></div>";
        echo "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cineMatch</title>

       <link rel="stylesheet" href="../style/global.css">
    <link rel="stylesheet" href="../style/watchpartyConf.css">
</head>
<body>

<div id="header">
    <h1>New Watchparty</h1>
</div>
    
<div class="hl headerHL"></div>

<div id="contentBox">



    <form action="watchpartyConf.php" method="POST">

    <div class="subHeader">
        Infos
    </div>

    <div id="infoInputBox">
        <input type="text" name="name" id="nameInput" placeholder="Name of the Watchparty" required>
        <input type="text" name="location" id="locationInput" placeholder="Where will you watch">

    </div>

    <div class="hl headerHL"></div>

    <div id="inviteContainer">
        <input type="text" name="searchUser" id="searchUser" placeholder="Search for Users to invite">
        
        
        <div id="friendContainer">
        <?php  display4Users();?>
    </div>
    <div class="hl headerHL"></div>

    </div>

    <div class="subHeader">
        Select Lists
    </div>
    <div id="listBox">
        <?php displayAllLists();?>
    </div>





    </form>



</div>

</body>
</html>