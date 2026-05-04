<?php
require_once "./dbConnection.php";

if(! isset($_SESSION['userID'])){

/*
    header("Location: ../userSys/index.html");
    exit;

    EIGENTLICHES VORGEHEN ABER ZUM TESTEN BLÖD
    */

    $_SESSION["userID"] = 1;
    $_SESSION["username"] = "test user";
}

$users = [];
 $query = "SELECT * FROM user where userID IN 
 (SELECT DISTINCT userID1 from friend where userID2 = ?
 UNION ALL
  SELECT DISTINCT userID2 from friend where userID1 =?)"; // hier muss die userID der eingeloggten Person rein

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $_SESSION["userID"], $_SESSION["userID"]);

    $stmt->execute();

    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);


$notifications = [];
$query = "SELECT * FROM notification where toID = ? AND status like 'pending'";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION["userID"]);

    $stmt->execute();

    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);

    if(isset($_GET['deleteUser']) && $_GET['deleteUser'] == "true"){
        $query = "DELETE FROM user where userID = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_SESSION["userID"]);

        $stmt->execute();

        session_destroy();
        echo json_encode(["success" => true]);
        header("Location: ../userSys/index.html");
        exit;
    }




function display4Users(){
      global $users;

   if(count($users) < 1){
    echo "<p id='noFriends'>No friends to invite yet</p>";
   }else{
    for($i = 0; $i < min(4,count($users)); $i++){
        echo "<div class='userBox'>";
        echo "<img class='avatar' src='../resource/img/" . $users[$i]['avatar'] . "' alt='Poster'>";
        echo "<h2>" . $users[$i]['username'] . "</h2>";
        echo "<div class='inviteBox'></div>";
        echo "</div>";
    }
   }
}

function displayLast4Notifications(){
    global $notifications;

    if(count($notifications) < 1){
        echo "<p id='noNotifications'>No notifications yet</p>";
       }else{




    for($i = 0; $i < min(4,count($notifications)); $i++){
        echo "<div class='notificationBox'>";

        echo "<h2>" . $notifications[count($notifications) - $i-1]['content'] . "</h2>";
        echo "<div class='BTNContainer'>";
        
        echo "<div class='declineBTN'></div>";
        echo "<div class='acceptBTN'></div>";
        echo "</div>";
        echo "</div>";
    }
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
    <link rel="stylesheet" href="../style/profile.css">
    <script src="../js/profile.js" defer></script>

</head>
<body>

    <div id="header">
        <h1>Watchpartys</h1>
    </div>

    <div class="hl headerHL" ></div>

    <div id="content">
        <div id="usernameContainer">
           
            
                <?php echo $_SESSION['username']; ?>
            
        </div>

        <div class="subHeader">
        Notifications
    </div>

        <div id="notificationsContainer">
        <?php displayLast4Notifications();?>
        </div>

        <div class="subHeader">
        Add Friends
    </div>
     <div id="inviteContainer">
        
        <div id="friendContainer">
        <?php  display4Users();?>
    </div>

    <input type="text" name="searchUser" id="searchUser" placeholder="Search for Users to invite">

    <div class="hl headerHL"></div>

    </div>



        <div id="footerButtonContainer">

        <a href="./Profile.php?deleteUser=true">
            <div id="deleteBTN" class="footerBTN">
                delete
            </div>
        </a>
            <div id="logoutBTN" class="footerBTN" onclick="logout()">
                log out
            </div>

        </div>
    </div>
    
</body>
</html>