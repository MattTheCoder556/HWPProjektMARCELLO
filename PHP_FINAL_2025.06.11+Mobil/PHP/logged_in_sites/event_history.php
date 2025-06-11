<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['session_token'])) {
    header("Location: ../login.php");
    exit;
}

try {
    $sessionToken = $_SESSION['session_token'];

    // Fetch user ID from the session token
    $stmt = $pdo->prepare("SELECT id_user FROM session_tokens WHERE token = :session_token AND expiry_date > NOW()");
    $stmt->execute([':session_token' => $sessionToken]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Invalid session or session expired');
    }

    $userId = $user['id_user'];

    // Fetch past events (events signed up for with an end date in the past)
    $stmt = $pdo->prepare("
        SELECT e.id_event, e.event_name, e.event_pic, e.start_date, e.end_date, e.place 
        FROM event_signups es
        JOIN events e ON es.event_id = e.id_event
        WHERE es.user_id = :user_id AND e.end_date < NOW()
        ORDER BY e.end_date DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    $pastEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Event History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php
    // Include header
    include_once 'logged_header.php';
    ?>

    <div class="container my-5">
        <h1 class="text-center">My Event History</h1>
        <?php if (empty($pastEvents)): ?>
            <p class="text-center mt-4">You haven't attended any past events.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($pastEvents as $event): ?>
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
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // Include footer
    include_once 'logged_footer.php';
    ?>
</body>
</html>
