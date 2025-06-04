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

    // Fetch events the user signed up for, excluding expired ones
    $stmtEvents = $pdo->prepare("SELECT e.* 
                                 FROM events e
                                 JOIN event_signups es ON e.id_event = es.event_id
                                 WHERE es.user_id = :user_id AND e.end_date >= NOW()
                                 ORDER BY e.start_date DESC");
    $stmtEvents->execute([':user_id' => $userId]);
    $events = $stmtEvents->fetchAll();

    // Return events as a JSON response
    if (empty($events)) {
        echo json_encode(["message" => "No upcoming events found."]);
    } else {
        echo json_encode($events); // Send the events as a JSON array
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Error fetching events: " . $e->getMessage()]);
}
