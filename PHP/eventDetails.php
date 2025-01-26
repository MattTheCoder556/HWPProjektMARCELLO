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
    $event = null;
    $comments = [];
    $userId = null;

    $baseURL = "http://localhost/HWP_2024/MammaMiaMarcello/PHP";

    // Fetch event details
    $eventResponse = @file_get_contents($baseURL . "/api.php?action=getEvent&id=" . $eventId);
    if ($eventResponse === false) {
        throw new Exception('Failed to fetch event details');
    }
    $event = json_decode($eventResponse, true);

    if (isset($event['error'])) {
        throw new Exception($event['error']);
    }

    if (isset($_SESSION['session_token'])) {
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

        // Fetch comments for the event
        $stmt = $pdo->prepare("
            SELECT c.id_comment, c.comment_text, c.rating, c.user_id, u.username, c.created_at
            FROM event_comments c
            JOIN users u ON c.user_id = u.id_user
            WHERE c.event_id = :event_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([':event_id' => $eventId]);
        $comments = $stmt->fetchAll();
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
        <!-- Event Details -->
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

        <!-- Comment Section -->
        <?php if (isset($_SESSION['session_token'])): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h3>Comments</h3>
                    <form method="POST" action="add_comment.php">
                        <input type="hidden" name="event_id" value="<?= htmlspecialchars($eventId) ?>">
                        <div class="mb-3">
                            <textarea name="comment_text" class="form-control" placeholder="Write your comment..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rating">Rate the Event:</label>
                            <select name="rating" class="form-select" required>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>

                    <hr>

                    <!-- Display Comments -->
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p><strong><?= htmlspecialchars($comment['username']) ?></strong> (<?= htmlspecialchars($comment['created_at']) ?>)</p>
                                    <p><?= htmlspecialchars($comment['comment_text']) ?></p>
                                    <p>Rating: <?= htmlspecialchars($comment['rating']) ?> / 5</p>
                                    <?php if ($comment['user_id'] == $userId): ?>
                                        <form method="POST" action="delete_comment.php" class="d-inline">
                                            <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id_comment']) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="chat-box">
    <h4>Live Chat with Event Owner</h4>
    <div id="chat-messages" class="chat-messages"></div>
    <form id="chat-form">
        <input type="text" id="chat-input" placeholder="Type your message..." required>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</div>
    <?php include_once 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
