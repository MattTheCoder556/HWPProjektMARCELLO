<?php
include_once "../config.php";
include_once "../functions.php";
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);

try {
    $username = $_SESSION['username']; // Retrieve the username from the session
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

    // Check if the user is the owner of the event
    $stmtEventOwner = $pdo->prepare("SELECT owner FROM events WHERE id_event = :event_id");
    $stmtEventOwner->execute([':event_id' => $eventId]);
    $event = $stmtEventOwner->fetch();

    if (!$event) {
        throw new Exception("Event not found.");
    }

    if ($event['owner'] == $userId) {
        throw new Exception("You cannot subscribe to your own event.");
    }

    // Check if the user is already signed up for the event
    $stmtSignup = $pdo->prepare("SELECT * FROM event_signups WHERE event_id = :event_id AND user_id = :user_id");
    $stmtSignup->execute([':event_id' => $eventId, ':user_id' => $userId]);
    if ($stmtSignup->fetch()) {
        throw new Exception("You are already signed up for this event!");
    }

    // Insert signup into the database
    $stmtInsert = $pdo->prepare("INSERT INTO event_signups (event_id, user_id) VALUES (:event_id, :user_id)");
    $stmtInsert->execute([':event_id' => $eventId, ':user_id' => $userId]);

    echo "You have successfully signed up for the event!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
