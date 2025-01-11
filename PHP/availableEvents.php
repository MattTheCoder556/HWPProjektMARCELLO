<?php
include 'header.php';

// Include your configuration for API endpoints
include 'config.php'; // Assume this file contains the API base URL and other configs

// Initialize variables
$userId = '';
$events = [];
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Get the logged-in user's ID via API
    if (isset($_SESSION['username']) && isset($_SESSION['session_token'])) {
        $username = $_SESSION['username'];
        $sessionToken = $_SESSION['session_token'];

        // Make an API request to get the user's ID
        $apiUrl = $apiBaseUrl . "/getUserId";
        $response = file_get_contents($apiUrl . "?username=" . urlencode($username) . "&session_token=" . urlencode($sessionToken));

        $user = json_decode($response, true);

        if ($user && isset($user['id_user'])) {
            $userId = $user['id_user'];
        }
    }

    // Prepare the API URL for fetching events
    $eventsApiUrl = $apiBaseUrl . "/getEvents?user_id=" . urlencode($userId);

    if ($searchTerm) {
        $eventsApiUrl .= "&search=" . urlencode($searchTerm);
    }

    // Fetch events using the API
    $response = file_get_contents($eventsApiUrl);
    $events = json_decode($response, true);

    if (!is_array($events)) {
        $events = []; // Ensure events is an array
    }
} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
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
 <h1 class="head1">Available Events</h1>
 <form method="GET" action="" class="search-bar">
    <input type="text" name="search" placeholder="Search for events..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
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