<?php
require_once '../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['session_token'])) {
    header("Location: ../login.php");
    exit;
}

try {
    $sessionToken = $_SESSION['session_token'];

    if (isset($sessionToken)) {
        include_once 'logged_header.php';
    } else {
        include_once 'header.php';
    }

    // Fetch user from session_tokens
    $stmt = $pdo->prepare("SELECT id_user FROM session_tokens WHERE token = :session_token AND expiry_date > NOW()");
    $stmt->execute([':session_token' => $sessionToken]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Invalid session or session expired');
    }

    $userId = $user['id_user'];

    // If a POST request is made to remove an event
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_event_id'])) {
        $eventId = $_POST['remove_event_id'];

        $stmt = $pdo->prepare("DELETE FROM event_wishlists WHERE id_user = :user_id AND id_event = :event_id");
        $stmt->execute([':user_id' => $userId, ':event_id' => $eventId]);

        // Refresh the page to reflect changes
        header("Location: wishlist_events.php");
        exit;
    }

    // Fetch wishlisted events for the user
    $stmt = $pdo->prepare("
        SELECT e.id_event, e.event_name, e.event_pic, e.start_date, e.end_date, e.place 
        FROM event_wishlists ew
        JOIN events e ON ew.id_event = e.id_event
        WHERE ew.id_user = :user_id
    ");
    $stmt->execute([':user_id' => $userId]);
    $wishlistedEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">My Wishlisted Events</h1>
        <?php if (empty($wishlistedEvents)): ?>
            <p class="text-center mt-4">You have no events in your wishlist.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($wishlistedEvents as $event): ?>
                    <div class="col-md-4">
                        <div class="card shadow mb-4">
                            <img src="<?= htmlspecialchars($event['event_pic']) ?>" 
                                class="card-img-top" 
                                alt="<?= htmlspecialchars($event['event_name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($event['event_name']) ?></h5>
                                <p><strong>Start Date:</strong> <?= htmlspecialchars($event['start_date']) ?></p>
                                <p><strong>End Date:</strong> <?= htmlspecialchars($event['end_date']) ?></p>
                                <p><strong>Location:</strong> <?= htmlspecialchars($event['place']) ?></p>
                                <a href="../eventDetails.php?id=<?= $event['id_event'] ?>" class="btn btn-primary mb-2">View Event</a>
                                
                                <!-- Remove from Wishlist Button -->
                                <form method="POST" action="wishlist_events.php">
                                    <input type="hidden" name="remove_event_id" value="<?= htmlspecialchars($event['id_event']) ?>">
                                    <button type="submit" class="btn btn-danger">Remove from Wishlist</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
include_once 'logged_footer.php';
?>
</body>
</html>
