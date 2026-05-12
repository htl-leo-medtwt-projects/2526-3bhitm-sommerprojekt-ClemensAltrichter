<?php
require_once "./dbConnection.php";


if(!isset($_SESSION['userID'])){
    header("Location: ../userSys/index.html");
    exit;
}
$allUsedLists = [];


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


        if(isset($_GET['getLists']) && $_GET['getLists'] == "true"){
            echo json_encode($lists);
        }

        if(isset($_GET['getFriends']) && $_GET['getFriends'] == "true"){
            echo json_encode($users);
        }



function display4Users(){
   // $friends = getUsers();
   global $users;

   if(count($users) < 1){
    echo "<p id='noFriends'>No friends to invite yet</p>";
   }


    for($i = 0; $i < min(4,count($users)); $i++){
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
        echo "<div class='listCheckBox'  onclick='addListToWatchparty( ". $lists[$i]['listID'] .", this )'></div>";
        echo "</div>";
    }

}




//---------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json');

    try {

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            throw new Exception("Keine Daten empfangen");
        }

        $name     = $data['name'] ?? '';
        $location = $data['location'] ?? '';
        $lists    = $data['lists'] ?? [];
        $friends  = $data['friends'] ?? [];

        if (empty($name)) {
            throw new Exception("Name fehlt");
        }

        if (empty($lists)) {
            throw new Exception("Bitte mindestens eine Liste auswählen");
        }

        $userID = 1; // später Session!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $date = date("Y-m-d");
        $status = "open";

        $conn->begin_transaction();

        // WATCHPARTY
        $stmt = $conn->prepare("
            INSERT INTO watchparty (userID, status, name, date)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param("isss", $userID, $status, $name, $date);
        $stmt->execute();

        $watchpartyID = $stmt->insert_id;

        // LISTS
        $stmtList = $conn->prepare("
            INSERT INTO partylist (listID, watchpartyID)
            VALUES (?, ?)
        ");

        foreach ($lists as $listID) {
            $stmtList->bind_param("ii", $listID, $watchpartyID);
            $stmtList->execute();
        }

        // FRIENDS
        $stmtFriend = $conn->prepare("
            INSERT INTO partymember (userID, watchpartyID)
            VALUES (?, ?)
        ");

        // creator hinzufügen
        $stmtFriend->bind_param("ii", $userID, $watchpartyID);
        $stmtFriend->execute();

        foreach (array_unique($friends) as $friendID) {
            $stmtFriend->bind_param("ii", $friendID, $watchpartyID);
            $stmtFriend->execute();
        }

        $conn->commit();

        echo json_encode([
            "success" => true
        ]);

    } catch (Exception $e) {

        $conn->rollback();

        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }

    exit;
}



?>

