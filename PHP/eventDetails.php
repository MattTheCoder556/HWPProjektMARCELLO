<?php
require_once 'config.php';
if (isset($_SESSION['session_token']))
{
    include_once 'logged_in_sites/logged_header.php';
}
else
{
    include_once 'header.php';
}
try {
    $isSignedUp = false; // Default state for not logged-in users
    $userId = null;      // Default user ID when not logged in
    $username = null;    // Default username when not logged in

    // Establish the PDO connection
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Get the event ID from the query string
    if (empty($_GET['id'])) {
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

    // Check if the user is logged in
    if (isset($_SESSION['session_token'])) {
        // Fetch the username from the session
        $username = $_SESSION['username'];

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
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
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
                    alt="Event Image"   >

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
                <div class="row">
                    <div class="col-12 text-center">
                        <?php if (isset($_SESSION['session_token'])): ?>
                            <?php if (!$isSignedUp): ?>
                                <br>
                                <form method="POST" action="logged_in_sites/signUp.php">
                                    <input type="hidden" name="event_id" value="<?= htmlspecialchars($eventId) ?>">
                                    <button type="submit" class="btn btn-primary">Sign Up for Event</button>
                                </form>
                            <?php else: ?>
                                <br>
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
                            <br>
                            <a href="login.php" class="btn btn-warning">Login to Sign Up</a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
        include_once 'footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
