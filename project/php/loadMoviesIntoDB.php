<?php

// 🔑 API Key hier eintragen
define('TMDB_API_KEY', '275bf49551923ba94f65f82505ccf49b');

// 🔧 DB Verbindung
require_once 'dbConnection.php';

// ⚙️ Einstellungen
$maxPages = 50;
$delay = 200000; // 0.2 Sekunden

// 🧠 Prepared Statement
$stmt = $conn->prepare("
    INSERT INTO movie (tmdbID, title, overview, voteAVG, poster)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        overview = VALUES(overview),
        voteAVG = VALUES(voteAVG),
        poster = VALUES(poster)
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

for ($page = 1; $page <= $maxPages; $page++) {

    echo "Lade Seite $page...<br>";

    $url = "https://api.themoviedb.org/3/discover/movie?api_key=" . TMDB_API_KEY . "&page=" . $page;

    $response = file_get_contents($url);

    if ($response === FALSE) {
        echo "Fehler beim API Call<br>";
        continue;
    }

    $data = json_decode($response, true);

    if (!isset($data['results'])) {
        echo "Keine Daten erhalten<br>";
        continue;
    }

    foreach ($data['results'] as $movie) {

        $tmdbId = $movie['id'];
        $title = $movie['title'] ?? '';
        $overview = $movie['overview'] ?? '';
        $voteAVG = $movie['vote_average'] ?? 0;

        // Poster URL bauen
        $poster = '';
        if (!empty($movie['poster_path'])) {
            $poster = "https://image.tmdb.org/t/p/w500" . $movie['poster_path'];
        }

        $stmt->bind_param("issds", $tmdbId, $title, $overview, $voteAVG, $poster);
        $stmt->execute();
    }

    usleep($delay);
}

$stmt->close();
$conn->close();

echo "<br>Import abgeschlossen!";