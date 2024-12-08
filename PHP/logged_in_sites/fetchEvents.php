<head>
    <link rel="stylesheet" href="../../assets/css/event.css" type="text/css">
</head>
<?php
require_once '../config.php';
try
{
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    die("Database connection failed: " . $e->getMessage());
}

session_start();
$userId = $_SESSION['username'];
//Fetch user ID based on username
$stmtUser = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
$stmtUser->bindValue(':username', $userId, PDO::PARAM_STR);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("No user found with the provided username.");
}

$userId = $user['id_user'];

//Fetch events for the user ID
$stmtEvents = $pdo->prepare("SELECT id_event, event_name, event_type, start_date, place, description FROM events WHERE owner = :owner");
$stmtEvents->bindValue(':owner', $userId, PDO::PARAM_INT);
$stmtEvents->execute();

$events = $stmtEvents->fetchAll(PDO::FETCH_ASSOC);

//Display events
if (!empty($events)) {
    echo '<div class="list-group">';
    foreach ($events as $event) {
        echo '
        <a class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1" style="color: #F34213;">' . htmlspecialchars($event['event_name']) . '</h5>
                <small style="color: #BC5D2E;">' . htmlspecialchars($event['start_date']) . '</small>
            </div>
            <p class="mb-1 text-truncate">
                ' . htmlspecialchars($event['event_type']) . '
            </p>
            <p class="mb-1 text-truncate event-description" data-full-text="' . htmlspecialchars($event['description']) . '">
                ' . htmlspecialchars(substr($event['description'], 0, 120)) . '
            </p>
            <p>
                <small class="text-muted">Location: ' . htmlspecialchars($event['place']) . '</small>
            </p>
            <a href="../eventDetails.php?id=' . $event['id_event'] . '" class="btn btn-link read-more" style="color: #DE9151;">Read more...</a>
        </a>';
    }
    echo '</div>';
} else {
    echo '<p class="text-center text-muted">You don\'t have any events.</p>';
}
?>
