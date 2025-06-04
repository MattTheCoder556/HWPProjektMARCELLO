<?php
include 'header.php';
include 'config.php';

$userId = null;
$events = [];
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Check if the user is logged in
    $isLoggedIn = isset($_SESSION['username'], $_SESSION['session_token']);

    if ($isLoggedIn)
    {
        $username = $_SESSION['username'];
        $stmtUser = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
        $stmtUser->execute([':username' => $username]);
        $user = $stmtUser->fetch();

        if ($user)
        {
            $userId = $user['id_user'];
        }
    }

    if ($isLoggedIn && $userId !== null)
    {
        // Logged-in user: Exclude events where they are the owner
        $sql = "
            SELECT * 
            FROM events 
            WHERE public = 1 
              AND end_date >= NOW() 
              AND owner != :user_id
        ";
        $params = [':user_id' => $userId];
    }
    else
    {
        // Logged-out user: Fetch all public events
        $sql = "
            SELECT * 
            FROM events 
            WHERE public = 1 
              AND end_date >= NOW()
        ";
        $params = [];
    }

    // Add search condition if a search term is provided
    if (!empty($searchTerm)) {
        $sql .= " AND (event_name LIKE :search OR event_type LIKE :search OR description LIKE :search OR place LIKE :search)";
        $params[':search'] = '%' . $searchTerm . '%';
    }

    $sql .= " ORDER BY start_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Events</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/available.css'>
</head>
<body>
<h1 class="head1">Available Events</h1>
<form method="GET" action="" class="search-bar">
    <input type="text" name="search" placeholder="Search for events..." value="<?= htmlspecialchars($searchTerm) ?>">
    <button type="submit">Search</button>
</form>
<div class="events-list">
    <?php if (!empty($events)): ?>
        <?php foreach ($events as $event): ?>
            <div class="event-item">
                <h2><?= htmlspecialchars($event['event_name']) ?></h2>
                <div class="event_pic">
                    <img src="logged_in_sites/<?= htmlspecialchars($event['event_pic']) ?>" alt="Event Image" style="width: 200px; height: auto;">
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
    <?php else: ?>
        <p>No events found.</p>
    <?php endif; ?>
</div>
<?php
include 'footer.php';
?>
</body>
</html>
