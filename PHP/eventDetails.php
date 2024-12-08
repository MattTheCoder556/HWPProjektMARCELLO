<?php
include 'header.php';
require_once 'config.php';
//session_start();

try {
    // Check if the user is logged in
    if (!isset($_SESSION['session_token'])) {
        header("Location: index.php?error=not_logged_in");
        exit;
    }

    // Fetch the username from the session
    $username = $_SESSION['username'];

    // Establish the PDO connection
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Get the event ID from the query string
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("No event ID provided!");
    }

    $eventId = $_GET['id'];

    // Fetch the event details using the ID
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id_event = :id AND public = 1");
    $stmt->execute([':id' => $eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        throw new Exception("Event not found or not public!");
    }

    // Fetch the user ID based on the username
    $stmtUser = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
    $stmtUser->execute([':username' => $username]);
    $user = $stmtUser->fetch();

    if (!$user) {
        throw new Exception("Invalid user. No matching user found.");
    }

    $userId = $user['id_user'];

    // Check if the user is signed up for this event
    $stmtSignup = $pdo->prepare("SELECT * FROM event_signups WHERE event_id = :event_id AND user_id = :user_id");
    $stmtSignup->execute([':event_id' => $eventId, ':user_id' => $userId]);
    $isSignedUp = $stmtSignup->fetch();
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
                         alt="Event Image"
                         style="max-height: 300px; object-fit: cover;">
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
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <h3>Description</h3>
                        <p><?= htmlspecialchars($event['description']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
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
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
