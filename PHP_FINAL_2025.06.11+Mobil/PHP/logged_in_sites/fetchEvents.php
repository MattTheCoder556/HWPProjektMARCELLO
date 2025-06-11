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

    if (empty($events)) {
        echo "<p>You don't have any upcoming events.</p>";
    } else {
        foreach ($events as $event) {
            echo "<div class='event-item'>";
            echo "<h3>" . htmlspecialchars($event['event_name']) . "</h3>";
            echo "<p><strong>Type:</strong> " . htmlspecialchars($event['event_type']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($event['description']) . "</p>";
            echo "<p><strong>Start Date:</strong> " . htmlspecialchars($event['start_date']) . "</p>";
            echo "<p><strong>End Date:</strong> " . htmlspecialchars($event['end_date']) . "</p>";
            echo "<p><strong>Location:</strong> " . htmlspecialchars($event['place']) . "</p>";

            // "Check Event" button
            echo "<a href='../eventDetails.php?id=" . urlencode($event['id_event']) . "' class='btn btn-primary mt-2'>Check Event</a>";

            // "Invite People" button
            echo "<button 
        type='button' 
        class='btn btn-warning mt-2 ml-2' 
        onclick='openInviteModal(" . htmlspecialchars($event['id_event']) . ")'>
        <i class='fas fa-user-plus'></i> Invite People
      </button>";
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<p>Error fetching events: " . htmlspecialchars($e->getMessage()) . "</p>";
}