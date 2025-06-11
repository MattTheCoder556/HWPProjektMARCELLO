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

        // Redirect to events page (or another confirmation page)
        header("Location: profileMain.php");
        exit;
    } else {
        echo "<p class='text-danger'>You are not authorized to delete this event or it does not exist.</p>";
    }
}

    // Fetch events the user owns
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

            // "Check Event" button
            echo "<a href='../eventDetails.php?id=" . urlencode($event['id_event']) . "' class='btn btn-primary mt-2'>Check Event</a>";

            // "Invite People" button
            echo "<button 
                type='button' 
                class='btn btn-warning mt-2 ml-2' 
                onclick='openInviteModal(" . htmlspecialchars($event['id_event']) . ")'>
                <i class='fas fa-user-plus'></i> Invite People
            </button>";

            // "Manage wishlist" button
            echo "<button 
        type='button' 
        class='btn btn-success mt-2 ml-2' 
        onclick='openWishlistModal(" . htmlspecialchars($event['id_event']) . ")'>
        <i class='fas fa-list'></i> Manage Wishlist
      </button>";

            // "Edit Event" button
            echo "<a href='editEvent.php?id=" . urlencode($event['id_event']) . "' class='btn btn-secondary mt-2 ml-2'>
                <i class='fas fa-edit'></i> Edit Event
            </a>";

            // "Delete Event" button (only for the event owner)
            echo "<form method='POST' action='fetchEvents2.php' style='display: inline;' onsubmit='return confirm(\"Are you sure you want to delete this event?\");'>
            <input type='hidden' name='delete_event_id' value='" . htmlspecialchars($event['id_event']) . "'>
            <button type='submit' class='btn btn-danger mt-2 ml-2'>
                <i class='fas fa-trash'></i> Delete Event
            </button>
        </form>";

            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<p>Error fetching events: " . htmlspecialchars($e->getMessage()) . "</p>";
}
