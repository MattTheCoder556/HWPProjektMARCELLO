<?php
include 'header.php';
// Include database configuration and connect to the database
include 'config.php';

try {
    // Get the event ID from the query string
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("No event ID provided!");
    }

    $eventId = $_GET['id'];

    // Establish the PDO connection
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Fetch the event details using the ID
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id_event = :id AND public = 1");
    $stmt->execute([':id' => $eventId]);
    $event = $stmt->fetch();

    // If no event is found, handle it
    if (!$event) {
        throw new Exception("Event not found or not public!");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Details</title>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/eventDetails.css'>
</head>
<body>
    <div class="eventDesc">
        <h1><?= htmlspecialchars($event['event_name']) ?></h1>
        <br><br><br>
        <div class="eventPic">
            <img src="logged_in_sites/<?= htmlspecialchars($event['event_pic']) ?>" alt="Event Image" style="max-width: 400px; max-height: auto;">
        </div>
        <p><strong>Type:</strong> <?= htmlspecialchars($event['event_type']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($event['description']) ?></p>
        <p><strong>Start Date:</strong> <?= htmlspecialchars($event['start_date']) ?></p>
        <p><strong>End Date:</strong> <?= htmlspecialchars($event['end_date']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($event['place']) ?></p>
    </div>
</body>
</html>

<?php
include 'footer.php';
?>