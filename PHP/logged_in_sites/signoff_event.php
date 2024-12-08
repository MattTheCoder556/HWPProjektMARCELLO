<?php
require_once '../config.php';
session_start();

try {
    // Check if user is logged in
    if (!isset($_SESSION['session_token'])) {
        header("Location: index.php?error=not_logged_in");
        exit;
    }

    $username = $_SESSION['username'];
    $eventId = $_POST['event_id'] ?? null;

    // Validate event ID
    if (!$eventId) {
        throw new Exception("No event ID provided!");
    }

    // Establish the PDO connection
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Fetch the user ID based on the username
    $stmtUser = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
    $stmtUser->execute([':username' => $username]);
    $user = $stmtUser->fetch();

    if (!$user) {
        throw new Exception("Invalid user. No matching user found.");
    }

    $userId = $user['id_user'];

    // Check if the user is signed up for the event
    $stmtCheck = $pdo->prepare("SELECT * FROM event_signups WHERE event_id = :event_id AND user_id = :user_id");
    $stmtCheck->execute([':event_id' => $eventId, ':user_id' => $userId]);

    if (!$stmtCheck->fetch()) {
        throw new Exception("You are not signed up for this event!");
    }

    // Remove the user from the event_signups table
    $stmtDelete = $pdo->prepare("DELETE FROM event_signups WHERE event_id = :event_id AND user_id = :user_id");
    $stmtDelete->execute([':event_id' => $eventId, ':user_id' => $userId]);

    echo "You have successfully signed off from the event.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
