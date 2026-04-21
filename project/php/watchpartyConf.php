<?php






function displayAllUsers(){
    global $users;

    for($i = 0; $i < count($users); $i++){
        echo "<div class='userBox'>";
        echo "<img class='avatar' src='../resource/img/" . $users[$i]['avatar'] . "' alt='Poster'>";
        echo "<h2>" . $users[$i]['username'] . "</h2>";
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
</head>
<body>

<div id="header">
    New Watchparty
</div>
    
<div class="hl"></div>

<div id="contentBox">



    <form action="watchpartyConf.php" method="POST">

    <div class="subHeader">
        Infos
    </div>

    <div id="infoInputBox">
        <input type="text" name="name" id="nameInput" placeholder="Name of the Watchparty" required>
    </div>

    <div class="hl"></div>

    <div id="inviteBox">
        <input type="text" name="searchUser" id="searchUser" placeholder="Search for Users to invite">
        <div class="hl"></div>
        <?php // displayAllUsers();?>
    </div>

    <div class="subHeader">
        Select Lists
    </div>
    <div id="listBox">
        <?php // displayAllLists();?>
    </div>





    </form>



</div>

</body>
</html>