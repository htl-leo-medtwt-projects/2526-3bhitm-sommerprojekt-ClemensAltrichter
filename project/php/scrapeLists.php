<?php
require_once 'dbConnection.php';

if (!isset($_SESSION['userID'])) {
    header('Content-Type: application/json');
    echo json_encode(["code" => 401, "message" => "Unauthorized"]);
    exit;
}

$answer = array("code" => 404, "data" => []);

// ── FILM ZU LISTE HINZUFÜGEN ──────────────
if (isset($_GET['addMovie']) && isset($_GET['listID'])) {
    $movieID = $_GET['addMovie'];
    $listID  = $_GET['listID'];

    $stmt = $conn->prepare("INSERT INTO listmovie (listID, movieID,added) VALUES (?, ?,NOW())");
    $stmt->bind_param("ii", $listID, $movieID);

    if ($stmt->execute()) {
        $answer["code"] = 200;
    } else {
        $answer["code"] = 405;
    }
    $stmt->close();

// ── LISTEN DES USERS LADEN ────────────────
} else {
    $userID = $_SESSION['userID'];
    $stmt = $conn->prepare("SELECT * FROM list WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $answer["code"] = 200;
    $answer["data"] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

echo json_encode($answer);
$conn->close();
?>