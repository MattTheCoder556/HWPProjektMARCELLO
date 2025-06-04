<?php
include_once "../config.php";
include_once "../functions.php";
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);

try {
    // Establish the database connection
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Get the logged-in user's ID
    $username = $_SESSION['username'];
    $stmtUser = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
    $stmtUser->execute([':username' => $username]);
    $user = $stmtUser->fetch();

    if (!$user) {
        throw new Exception("User not found.");
    }

    $userId = $user['id_user'];

    // Handle Delete Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event_id'])) {
        $deleteEventId = $_POST['delete_event_id'];

        // Check if the event belongs to the logged-in user
        $stmtCheck = $pdo->prepare("SELECT * FROM events WHERE id_event = :id AND owner = :owner_id");
        $stmtCheck->execute([':id' => $deleteEventId, ':owner_id' => $userId]);
        $eventToDelete = $stmtCheck->fetch();

        if ($eventToDelete) {
            // Delete the event
            $stmtDelete = $pdo->prepare("DELETE FROM events WHERE id_event = :id");
            $stmtDelete->execute([':id' => $deleteEventId]);

            echo json_encode(["message" => "Event deleted successfully."]);
            exit;
        } else {
            echo json_encode(["error" => "You are not authorized to delete this event or it does not exist."]);
            exit;
        }
    }

    // Fetch events the user owns
    $stmtEvents = $pdo->prepare("SELECT * 
                                 FROM events
                                 WHERE owner = :owner_id
                                 ORDER BY start_date DESC");
    $stmtEvents->execute([':owner_id' => $userId]);
    $events = $stmtEvents->fetchAll();

    // Return events as a JSON response
    if (empty($events)) {
        echo json_encode(["message" => "You haven't created any events yet."]);
    } else {
        echo json_encode($events); // Send the events as a JSON array
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Error fetching events: " . $e->getMessage()]);
}
