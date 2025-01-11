<?php

require_once 'config.php';

// Include the header based on whether the user is logged in or not
if (isset($_SESSION['session_token'])) {
    include_once 'logged_in_sites/logged_header.php';
} else {
    include_once 'header.php';
}

$eventId = $_GET['id'] ?? null;
if (empty($eventId)) {
    die('No event ID provided!');
}

try {
    // Initialize variables
    $event = null;
    $isSignedUp = false;
    
    $baseURL = "http://localhost/HWP_2024/MammaMiaMarcello/PHP";
    
    // Fetch event details using the API (GET request to getEvent)
    $eventResponse = @file_get_contents($baseURL . "/api.php?action=getEvent&id=" . $eventId);
    if ($eventResponse === false) {
        throw new Exception('Failed to fetch event details');
    }
    $event = json_decode($eventResponse, true);

    if (isset($event['error'])) {
        throw new Exception($event['error']);
    }

    // If the user is logged in, fetch the user ID and check if they are signed up
    if (isset($_SESSION['session_token'])) {
        // Use session token directly to fetch user details
        $sessionToken = $_SESSION['session_token'];
        
        // Fetch user from session_tokens
        $stmt = $pdo->prepare("SELECT id_user FROM session_tokens WHERE token = :session_token AND expiry_date > NOW()");
        $stmt->execute([':session_token' => $sessionToken]);
        $user = $stmt->fetch();
        
        if ($user) {
            $userId = $user['id_user'];
        } else {
            throw new Exception('Invalid session or session expired');
        }

        // Check if the user is signed up for the event
        $signupQuery = "SELECT COUNT(*) FROM event_signups WHERE event_id = :event_id AND user_id = :user_id";
        $stmt = $pdo->prepare($signupQuery);
        $stmt->execute(['event_id' => $eventId, 'user_id' => $userId]);
        $isSignedUp = $stmt->fetchColumn() > 0;
    }

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Event Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/eventDetails.css">
</head>
<body>
    <div class="container my-5">
        <!-- Event Image and Details -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <img src="logged_in_sites/<?= htmlspecialchars($event['event_pic']) ?>" 
                        class="card-img-top img-fluid" 
                        alt="Event Image">
                    <div class="card-body">
                        <h2 class="card-title"><?= htmlspecialchars($event['event_name']) ?></h2>
                        <p><strong>Type:</strong> <?= htmlspecialchars($event['event_type']) ?></p>
                        <p><strong>Description:</strong> <?= htmlspecialchars($event['description']) ?></p>
                        <p><strong>Start Date:</strong> <?= htmlspecialchars($event['start_date']) ?></p>
                        <p><strong>End Date:</strong> <?= htmlspecialchars($event['end_date']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($event['place']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <?php if (isset($_SESSION['session_token'])): ?>
                    <?php if (!$isSignedUp): ?>
                        <form method="POST" action="logged_in_sites/signUp.php">
                            <input type="hidden" name="event_id" value="<?= htmlspecialchars($eventId) ?>">
                            <button type="submit" class="btn btn-primary">Sign Up for Event</button>
                        </form>
                    <?php else: ?>
                        <form action="logged_in_sites/signoff_event.php" method="POST" class="mt-3">
                            <input type="hidden" name="event_id" value="<?= htmlspecialchars($eventId) ?>">
                            <button type="submit" class="btn btn-danger">Sign Off</button>
                        </form>
                        <?php
                        $eventName = rawurlencode($event['event_name']);
                        $startDate = rawurlencode(date('Ymd\THis\Z', strtotime($event['start_date']))); 
                        $endDate = rawurlencode(date('Ymd\THis\Z', strtotime($event['end_date'])));
                        $location = rawurlencode($event['place']);
                        $details = rawurlencode($event['description']);
                        ?>
                        <a class="btn btn-primary mt-3" href="https://calendar.google.com/calendar/u/0/r/eventedit?text=<?= $eventName ?>&dates=<?= $startDate ?>/<?= $endDate ?>&location=<?= $location ?>&details=<?= $details ?>" target="_blank">Add to Google Calendar</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-warning">Login to Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include_once 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
