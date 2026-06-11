<?php
require_once 'dbConnection.php';

if (!isset($_SESSION['userID'])) {
    header('Content-Type: application/json');
    echo json_encode(["code" => 401, "message" => "Unauthorized"]);
    exit;
}

header('Content-Type: application/json');

$answer = ["code" => 404, "data" => []];
$userID = $_SESSION['userID'];

// ── FILME LADEN ───────────────────────────
// Alle Filme aus den Listen aller Members dieser Party
if (isset($_GET['getMovies'])) {
    $watchpartyID = $_GET['getMovies'];

    $stmt = $conn->prepare("
        SELECT DISTINCT m.movieID, m.title, m.overview, m.poster, m.voteAVG
        FROM movie m
        JOIN listmovie lm ON lm.movieID = m.movieID
        JOIN partylist pl ON pl.listID = lm.listID
        WHERE pl.watchpartyID = ?
        ORDER BY RAND()
    ");
    $stmt->bind_param("i", $watchpartyID);
    $stmt->execute();
    $result = $stmt->get_result();
    $movies = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $answer["code"] = 200;
    $answer["data"] = $movies;
}

// ── SWIPE SPEICHERN ───────────────────────
if (isset($_GET['swipe'])) {
    $watchpartyID = $_GET['watchpartyID'] ?? 0;
    $movieID      = $_GET['movieID'] ?? 0;
    $liked        = $_GET['liked'] === 'true' ? 1 : 0;

    $stmt = $conn->prepare("
        INSERT INTO swipes (userID, movieID, watchpartyID, liked)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE liked = ?
    ");
    $stmt->bind_param("iiiii", $userID, $movieID, $watchpartyID, $liked, $liked);
    $stmt->execute();
    $stmt->close();

    $answer["code"] = 200;
}

// ── PRÜFEN OB ALLE FERTIG SIND ────────────
if (isset($_GET['checkDone'])) {
    $watchpartyID = $_GET['checkDone'];

    // Anzahl Members
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total FROM partymember
        WHERE watchpartyID = ? AND status = 'joined'
    ");
    $stmt->bind_param("i", $watchpartyID);
    $stmt->execute();
    $totalMembers = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Anzahl Filme in dieser Party
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT m.movieID) AS total
        FROM movie m
        JOIN listmovie lm ON lm.movieID = m.movieID
        JOIN partylist pl ON pl.listID = lm.listID
        WHERE pl.watchpartyID = ?
    ");
    $stmt->bind_param("i", $watchpartyID);
    $stmt->execute();
    $totalMovies = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Anzahl User die alle Filme geswipt haben
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS done FROM (
            SELECT userID FROM swipes
            WHERE watchpartyID = ?
            GROUP BY userID
            HAVING COUNT(*) >= ?
        ) AS finished
    ");
    $stmt->bind_param("ii", $watchpartyID, $totalMovies);
    $stmt->execute();
    $doneMemebers = $stmt->get_result()->fetch_assoc()['done'];
    $stmt->close();

    $allDone = $doneMemebers >= $totalMembers;

    // Wenn alle fertig → Party auf finished setzen
    if ($allDone) {
        $stmt = $conn->prepare("UPDATE watchparty SET status = 'finished' WHERE watchpartyID = ?");
        $stmt->bind_param("i", $watchpartyID);
        $stmt->execute();
        $stmt->close();

        // chosenMovieID — Film mit den meisten Likes
        $stmt = $conn->prepare("
            SELECT movieID FROM swipes
            WHERE watchpartyID = ? AND liked = 1
            GROUP BY movieID
            ORDER BY COUNT(*) DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $watchpartyID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row) {
            $stmt = $conn->prepare("UPDATE watchparty SET chosenMovieID = ? WHERE watchpartyID = ?");
            $stmt->bind_param("ii", $row['movieID'], $watchpartyID);
            $stmt->execute();
            $stmt->close();
        }

    }

    $answer["code"] = 200;
    $answer["data"] = [
        "allDone"      => $allDone,
        "doneMemebers" => (int)$doneMemebers,
        "totalMembers" => (int)$totalMembers
    ];
}

// ── ERGEBNIS LADEN ────────────────────────
if (isset($_GET['getResult'])) {
    $watchpartyID = $_GET['getResult'];

    $stmt = $conn->prepare("
        SELECT m.movieID, m.title, m.poster, m.overview, m.voteAVG,
               COUNT(*) AS likes
        FROM swipes s
        JOIN movie m ON m.movieID = s.movieID
        WHERE s.watchpartyID = ? AND s.liked = 1
        GROUP BY m.movieID
        ORDER BY likes DESC
    ");
    $stmt->bind_param("i", $watchpartyID);
    $stmt->execute();
    $result = $stmt->get_result();
    $movies = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $answer["code"] = 200;
    $answer["data"] = $movies;
}

echo json_encode($answer);
$conn->close();
?>
