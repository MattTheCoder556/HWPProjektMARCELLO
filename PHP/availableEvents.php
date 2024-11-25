<?php
// Include database configuration and connect to the database
include 'config.php'; // Include your database configuration

try {
    // Establish the PDO connection using the variables from db_config.php
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Handle errors with exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch results as an associative array
    ]);

    // Check if a search term is provided
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    if ($searchTerm) {
        // Prepare the query to filter events by the search term
        $stmt = $pdo->prepare("SELECT * FROM events WHERE public = 1 AND 
            (event_name LIKE :search OR event_type LIKE :search OR description LIKE :search OR place LIKE :search)
            ORDER BY start_date DESC");
        $stmt->execute([':search' => '%' . $searchTerm . '%']);
    } else {
        // Prepare the query to fetch all events
        $stmt = $pdo->prepare("SELECT * FROM events WHERE public = 1 ORDER BY start_date DESC");
        $stmt->execute();
    }

    $events = $stmt->fetchAll(); // Fetch all results into an array
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage(); // Handle any database connection errors
    exit;
}


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Events</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/available.css'>
</head>
<body>
<?php
include 'header.php';
?>
 <h1 class="head1">Available Events</h1>
 <form method="GET" action="" class="search-bar">
    <input type="text" name="search" placeholder="Search for events..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    <button type="submit">Search</button>
</form>
 <div class="events-list">
    <?php foreach ($events as $event): ?>
        <div class="event-item">
            <h2><?= htmlspecialchars($event['event_name']) ?></h2>
            <div class="event_pic">
            <img src="<?= htmlspecialchars($event['event_pic']) ?>" alt="Event Image" style="width: 200px; height: auto;">
            </div>
            <p><strong>Type:</strong> <?= htmlspecialchars($event['event_type']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($event['description']) ?></p>
            <p><strong>Start Date:</strong> <?= htmlspecialchars($event['start_date']) ?></p>
            <p><strong>End Date:</strong> <?= htmlspecialchars($event['end_date']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($event['place']) ?></p>
            <br>
            <a href="eventDetails.php?id=<?= urlencode($event['id_event']) ?>" class="detailsButton">Details</a>
            </div>
    <?php endforeach; ?>
</div>
<?php
include 'footer.php';
?>
</body>
</html>