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
    $stmtEvents = $pdo->prepare("SELECT * 
                                 FROM events
                                 WHERE owner = :owner_id
                                 ORDER BY start_date DESC");
    $stmtEvents->execute([':owner_id' => $userId]);
    $events = $stmtEvents->fetchAll();

    if (empty($events)) {
        echo "<p>You haven't created any events yet.</p>";
    } else {
        foreach ($events as $event) {
            echo "<div class='event-item mb-4 p-3 border rounded shadow-sm'>";
            echo "<h3 style='color: #F34213;'>" . htmlspecialchars($event['event_name']) . "</h3>";
            echo "<p><strong>Type:</strong> " . htmlspecialchars($event['event_type']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($event['description']) . "</p>";
            echo "<p><strong>Start Date:</strong> " . htmlspecialchars($event['start_date']) . "</p>";
            echo "<p><strong>End Date:</strong> " . htmlspecialchars($event['end_date']) . "</p>";
            echo "<p><strong>Location:</strong> " . htmlspecialchars($event['place']) . "</p>";
            echo "<a href='../eventDetails.php?id=" . urlencode($event['id_event']) . "' class='btn btn-primary mt-2'>Check Event</a>";
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<p>Error fetching events: " . htmlspecialchars($e->getMessage()) . "</p>";
}