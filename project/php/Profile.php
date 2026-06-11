<?php
/*
require_once "./dbConnection.php";



if(! isset($_SESSION['userID'])){
    header("Location: ../userSys/index.html");
    exit;    
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
        echo "<div class='acceptBTN' onclick='acceptInvite(". $notifications[count($notifications) - $i-1]['partyID'] .")'></div>";
        echo "</div>";
        echo "</div>";
    }
       }
}




*/

require_once "./dbConnection.php";

if (!isset($_SESSION['userID'])) {
    header("Location: ../userSys/index.html");
    exit;
}

$userID = $_SESSION['userID'];

// ── DELETE USER (jetzt als GET mit JSON response) ──
if (isset($_GET['deleteUser']) && $_GET['deleteUser'] == "true") {
    $stmt = $conn->prepare("DELETE FROM user WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->close();
    session_destroy();
    header("Location: ../userSys/index.html");
    exit;
}

// ── REJECT INVITE ──────────────────────────────────
/*
if (isset($_GET['rejectInvite'])) {
    $partyID = $_GET['rejectInvite'];
    header('Content-Type: application/json');

    $stmt = $conn->prepare("UPDATE notification SET status = 'rejected' WHERE partyID = ? AND toID = ?");
    $stmt->bind_param("ii", $partyID, $userID);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["code" => 200]);
    exit;
}*/
if (isset($_GET['rejectInvite'])) {
    $notificationID = $_GET['rejectInvite'];
    header('Content-Type: application/json');

    $stmt = $conn->prepare("UPDATE notification SET status = 'rejected' WHERE id = ? AND toID = ?");
    $stmt->bind_param("ii", $notificationID, $userID);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["code" => 200]);
    exit;
}

// ── FREUNDE LADEN ──────────────────────────────────
$users = [];
$stmt = $conn->prepare("SELECT * FROM user WHERE userID NOT IN 
    (SELECT DISTINCT userID1 FROM friend WHERE userID2 = ?
     UNION ALL
     SELECT DISTINCT userID2 FROM friend WHERE userID1 = ?)
     AND userID != ?");
$stmt->bind_param("iii", $userID, $userID, $userID);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── NOTIFICATIONS MIT ABSENDER-USERNAME ───────────
$notifications = [];
$stmt = $conn->prepare("
    SELECT n.*, u.username AS senderName
    FROM notification n
    JOIN user u ON u.userID = n.fromID
    WHERE n.toID = ? AND n.status = 'pending'
    ORDER BY n.id DESC
    LIMIT 4
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function display4Users() {
    global $users;
    if (count($users) < 1) {
        echo "<p id='noFriends'>No friends to invite yet</p>";
    } else {
        for ($i = 0; $i < min(4, count($users)); $i++) {
            echo "<div class='userBox'>";
            echo "<img class='avatar' src='../resource/img/" . $users[$i]['avatar'] . "' alt='avatar'>";
            echo "<h2>" . $users[$i]['username'] . "</h2>";
            echo "<div class='inviteBox' data-userid='" . $users[$i]['userID'] . "' onclick='sendFriendRequest(" . $users[$i]['userID'] . ", this)'></div>";
            echo "</div>";
        }
    }
}
/*
function displayLast4Notifications() {
    global $notifications;
    if (count($notifications) < 1) {
        echo "<p id='noNotifications'>No notifications yet</p>";
    } else {
        foreach ($notifications as $n) {
            echo "<div class='notificationBox'>";
            echo "<h2>" . htmlspecialchars($n['senderName']) . " invited you</h2>";
            echo "<div class='BTNContainer'>";
            echo "<div class='declineBTN' onclick='rejectInvite(" . $n['partyID'] . ", this.closest(\".notificationBox\"))'></div>";
            echo "<div class='acceptBTN' onclick='acceptInvite(" . $n['partyID'] . ")'></div>";
            echo "</div>";
            echo "</div>";
        }
    }
}*/
function displayLast4Notifications() {
    global $notifications;
    if (count($notifications) < 1) {
        echo "<p id='noNotifications'>No notifications yet</p>";
    } else {
        /*
        foreach ($notifications as $n) {
            echo "<div class='notificationBox' data-notificationid='" . $n['id'] . "' data-partyid='" . ($n['partyID'] ?? '') . "'>";
            echo "<h2>" . htmlspecialchars($n['senderName']) . " invited you</h2>";
            echo "<div class='BTNContainer'>";
            echo "<div class='declineBTN' onclick='rejectInvite(this)'></div>";
            echo "<div class='acceptBTN' onclick='acceptInvite(this)'></div>";
            echo "</div>";
            echo "</div>";
        }
            */
        foreach ($notifications as $n) {
            echo "<div class='notificationBox' data-notificationid='" . $n['id'] . "' data-partyid='" . ($n['partyID'] ?? '') . "' data-fromuserid='" . $n['fromID'] . "'>";
            
            if ($n['content'] === 'invite') {
                echo "<h2>" . htmlspecialchars($n['senderName']) . " invited you</h2>";
                echo "<div class='BTNContainer'>";
                echo "<div class='declineBTN' onclick='rejectInvite(this)'></div>";
                echo "<div class='acceptBTN' onclick='acceptInvite(this)'></div>";
                echo "</div>";
            } else if ($n['content'] === 'friendrequest') {
                echo "<h2>" . htmlspecialchars($n['senderName']) . " sent you a friend request</h2>";
                echo "<div class='BTNContainer'>";
                echo "<div class='declineBTN' onclick='rejectInvite(this)'></div>";
                echo "<div class='acceptBTN' onclick='acceptFriendRequest(this)'></div>";
                echo "</div>";
            }
            echo "</div>";
        }
    }
    

}



// ── USER SUCHEN ───────────────────────────
if (isset($_GET['searchUser'])) {
    header('Content-Type: application/json');
    $search = '%' . $_GET['searchUser'] . '%';

    $stmt = $conn->prepare("
        SELECT userID, username, avatar FROM user
        WHERE username LIKE ? AND userID != ?
        LIMIT 8
    ");
    $stmt->bind_param("si", $search, $userID);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode(["code" => 200, "data" => $results]);
    exit;
}

// ── FRIEND REQUEST SENDEN ─────────────────
if (isset($_GET['sendRequest'])) {
    header('Content-Type: application/json');
    $toUserID = $_GET['sendRequest'];

    // Prüfen ob bereits eine Freundschaft/Request existiert
    $stmt = $conn->prepare("
        SELECT * FROM friend 
        WHERE (userID1 = ? AND userID2 = ?) OR (userID1 = ? AND userID2 = ?)
    ");
    $stmt->bind_param("iiii", $userID, $toUserID, $toUserID, $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["code" => 409, "message" => "Already friends or request pending"]);
        exit;
    }
    $stmt->close();

    // Friend Request eintragen
    $stmt = $conn->prepare("INSERT INTO friend (userID1, userID2, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $userID, $toUserID);
    $stmt->execute();
    $stmt->close();

    // Notification senden
    $stmt = $conn->prepare("INSERT INTO notification (fromID, toID, content, status) VALUES (?, ?, 'friendrequest', 'pending')");
    $stmt->bind_param("ii", $userID, $toUserID);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["code" => 200]);
    exit;
}

// ── FRIEND REQUEST ANNEHMEN ───────────────
if (isset($_GET['acceptRequest'])) {
    header('Content-Type: application/json');
    $fromUserID = $_GET['acceptRequest'];
    $notificationID = $_GET['notificationID'] ?? null;

    $stmt = $conn->prepare("UPDATE friend SET status = 'accepted' WHERE userID1 = ? AND userID2 = ?");
    $stmt->bind_param("ii", $fromUserID, $userID);
    $stmt->execute();
    $stmt->close();

    if ($notificationID) {
        $stmt = $conn->prepare("UPDATE notification SET status = 'accepted' WHERE id = ? AND toID = ?");
        $stmt->bind_param("ii", $notificationID, $userID);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(["code" => 200]);
    exit;
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

       <script src="../js/auth.js" defer></script>
    <script src="../js/profile.js" defer></script>

</head>
<body>
 
    <div id="header" onclick="location.href='../pages/browser.html'">
        <h1>Profile</h1>
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

    <input type="text" id="searchUser" placeholder="Search for Users to invite" oninput="handleUserSearch(this.value)">
    <div id="searchResults"></div>

    <div class="hl headerHL"></div>

    </div>



        <div id="footerButtonContainer">

        <div id="deleteBTN" class="footerBTN" onclick="deleteAccount()">delete</div>

            <div id="logoutBTN" class="footerBTN" onclick="logout()">
                log out
            </div>

        </div>
    </div>
    
</body>
</html>