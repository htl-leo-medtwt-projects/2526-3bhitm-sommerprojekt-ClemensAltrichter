<?php
require_once 'dbConnection.php';

if(!isset($_SESSION['userID'])){
    header("Location: ../userSys/index.html");
    exit;
}

$answer = array(
    "code" => 404,
    "data" => []
);

$query = "SELECT *,
 (SELECT poster from movie where movieID = chosenMovieID) poster,
 (SELECT title from movie where movieID = chosenMovieID) title
  FROM watchparty";

if($conn -> query($query)){
    $result = $conn -> query($query);
    $watchpartys = $result -> fetch_all(MYSQLI_ASSOC);

    $answer['code'] = 200;
    $answer['data'] = $watchpartys;

}




// ── WARTERAUM STATUS ABFRAGEN ─────────────
if (isset($_GET['getPartyStatus'])) {
    $partyID = $_GET['getPartyStatus'];

    /*
    $stmt = $conn->prepare("
        SELECT w.status, w.name AS partyName, w.userID AS hostID,
               u.username, u.avatar, pm.userID,
               IF(w.userID = pm.userID, 1, 0) AS isHost
        FROM watchparty w
        JOIN partymember pm ON pm.watchpartyID = w.watchpartyID
        JOIN user u ON u.userID = pm.userID
        WHERE w.watchpartyID = ?
    ");
    */
    
        $stmt = $conn->prepare("
    SELECT w.status, w.name AS partyName, w.userID AS hostID,
           u.username, u.avatar, pm.userID, pm.status AS memberStatus,
           IF(w.userID = pm.userID, 1, 0) AS isHost
    FROM watchparty w
    JOIN partymember pm ON pm.watchpartyID = w.watchpartyID
    JOIN user u ON u.userID = pm.userID
    WHERE w.watchpartyID = ?
    ");


    $stmt->bind_param("i", $partyID);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    $answer["code"] = 200;
    $answer["data"] = [
        "status"    => $rows[0]["status"] ?? "open",
        "partyName" => $rows[0]["partyName"] ?? "",
        "members"   => $rows
    ];
    $stmt->close();
}

// ── INVITE ANNEHMEN ───────────────────────
if (isset($_GET['acceptInvite'])) {
    $partyID = $_GET['acceptInvite'];
    $userID  = $_SESSION['userID'];

    $stmt = $conn->prepare("SELECT watchpartyID FROM partymember WHERE watchpartyID = ? AND userID = ?");
    $stmt->bind_param("ii", $partyID, $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO partymember (watchpartyID, userID) VALUES (?, ?)");
        $stmt->bind_param("ii", $partyID, $userID);
        $stmt->execute();

        
    }
    $stmt = $conn->prepare("UPDATE partymember SET status = 'joined' WHERE watchpartyID = ? AND userID = ?");
        $stmt->bind_param("ii", $partyID, $userID);
        $stmt->execute();

    $stmt->close();

    // hier partyID statt watchpartyID weil notification-Tabelle so heißt
    $stmt = $conn->prepare("UPDATE notification SET status = 'accepted' WHERE partyID = ? AND toID = ?");
    $stmt->bind_param("ii", $partyID, $userID);
    $stmt->execute();
    $stmt->close();

    $answer["code"] = 200;
    $answer["data"] = ["partyID" => $partyID];
}

// ── PARTY STARTEN (nur Host) ──────────────
if (isset($_GET['startParty'])) {
    $partyID = $_GET['startParty'];

    $stmt = $conn->prepare("UPDATE watchparty SET status = 'active' WHERE watchpartyID = ? AND userID = ?");
    $stmt->bind_param("ii", $partyID, $_SESSION['userID']);
    $stmt->execute();
    $stmt->close();

    $answer["code"] = 200;
}






echo json_encode($answer);



?>