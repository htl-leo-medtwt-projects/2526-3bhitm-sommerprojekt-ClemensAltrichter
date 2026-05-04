<?php
require_once "../php/dbConnection.php"; // session_start() ist bereits darin enthalten

header("Content-Type: application/json");

$action = $_POST["action"] ?? "";

// ── REGISTER ──────────────────────────────────────────────
if ($action === "register") {
    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (!$username || !$email || !$password) {
        echo json_encode(["success" => false, "message" => "Alle Felder ausfüllen."]);
        exit;
    }

    // Check ob Username oder Email bereits existiert
    $stmt = $conn->prepare("SELECT userID FROM user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Username oder E-Mail bereits vergeben."]);
        exit;
    }
    $stmt->close();

    // Avatar: erster Buchstabe des Usernamens
    $avatar = strtoupper($username[0]);

    $stmt = $conn->prepare("INSERT INTO user (username, email, password, avatar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $avatar);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true, "message" => "Account erstellt! Du kannst dich jetzt anmelden."]);
    exit;
}

// ── LOGIN ─────────────────────────────────────────────────
if ($action === "login") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (!$username || !$password) {
        echo json_encode(["success" => false, "message" => "Username und Passwort eingeben."]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || $user["password"] !== $password) {
        echo json_encode(["success" => false, "message" => "Falscher Username oder Passwort."]);
        exit;
    }

    // Session setzen
    $_SESSION["userID"]   = $user["userID"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["avatar"]   = $user["avatar"];
    $_SESSION["email"]    = $user["email"];

    echo json_encode(["success" => true, "message" => "Willkommen, " . $user["username"] . "!"]);
    exit;
}

// ── LOGOUT ────────────────────────────────────────────────
if ($action === "logout") {
    session_destroy();
    echo json_encode(["success" => true]);
    exit;
}

// ── SESSION CHECK ─────────────────────────────────────────
if ($action === "check") {
    if (isset($_SESSION["userID"])) {
        echo json_encode([
            "loggedIn" => true,
            "username" => $_SESSION["username"],
            "avatar"   => $_SESSION["avatar"],
            "email"    => $_SESSION["email"]
        ]);
    } else {
        echo json_encode(["loggedIn" => false]);
    }
    exit;
}

echo json_encode(["error" => "Unbekannte Aktion."]);
?>
