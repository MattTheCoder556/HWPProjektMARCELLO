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
    $isSignedUp = false;
    $isWishlisted = false;

    $baseURL1 = "http://localhost/HWPProjektMarcello/PHP";
    $baseURL2 = "http://localhost/HWP_2024/HWPProjektMarcello/PHP";

    // Fetch event details
    $eventResponse = @file_get_contents($baseURL1 . "/api.php?action=getEvent&id=" . $eventId);
    if ($eventResponse === false) {
        throw new Exception('Failed to fetch event details');
    }
    $event = json_decode($eventResponse, true);
    $commentsEnabled = $event['comments_enabled'];
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

        // Check if the user is signed up for the event
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_signups WHERE event_id = :event_id AND user_id = :user_id");
        $stmt->execute(['event_id' => $eventId, 'user_id' => $userId]);
        $isSignedUp = $stmt->fetchColumn() > 0;

        // Check if the user has wishlisted the event
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_wishlists WHERE id_event = :event_id AND id_user = :user_id");
        $stmt->execute(['event_id' => $eventId, 'user_id' => $userId]);
        $isWishlisted = $stmt->fetchColumn() > 0;

        // Fetch invited people and their statuses
        $filterStatus = $_GET['status'] ?? null;
        $query = "
            SELECT u.username, u.firstname, u.lastname, i.status 
            FROM event_invites i
            JOIN users u ON i.id_user = u.id_user
            WHERE i.id_event = :event_id
        ";
        if ($filterStatus) {
            $query .= " AND i.status = :status";
        }
        $stmt = $pdo->prepare($query);
        $params = [':event_id' => $eventId];
        if ($filterStatus) {
            $params[':status'] = $filterStatus;
        }
        $stmt->execute($params);
        $invitedPeople = $stmt->fetchAll();

        // Fetch comments for the event
            $stmt = $pdo->prepare("
            SELECT c.id_comment, c.comment_text, c.rating, c.created_at, c.user_id, u.username
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

$canComment = false;
if (isset($_SESSION['session_token']) && $event['comments_enabled']) {
    // lookup user_id
    $stmt = $pdo->prepare("
      SELECT u.id_user
      FROM users u
      JOIN session_tokens s ON s.id_user = u.id_user
      WHERE s.token = :token AND s.expiry_date > NOW()
    ");
    $stmt->execute([':token' => $_SESSION['session_token']]);
    $me = $stmt->fetch();

    if ($me) {
        // only after event end
        $now = new DateTime;
        $eventEnds = new DateTime($event['end_date']);
        if ($eventEnds < $now)
        {
            $stmt = $pdo->prepare("
              SELECT COUNT(*) 
              FROM event_comments 
              WHERE event_id = :event_id AND user_id = :user_id
            ");
            $stmt->execute([
                ':event_id' => $eventId,
                ':user_id'  => $me['id_user']
            ]);
            $already = $stmt->fetchColumn() > 0;
            $canComment = ! $already;
        }
    }
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

        <!-- Display sign-up and wishlist buttons -->
        <div class="row">
            <div class="col-12 text-center">
                <?php if (isset($_SESSION['session_token'])): ?>
                <?php if (!$isSignedUp): ?>
                    <form method="POST" action="logged_in_sites/signUp.php">
                        <input type="hidden" name="event_id" value="<?= htmlspecialchars($eventId) ?>">
                        <button type="submit" class="btn btn-primary">Sign Up for Event</button>
                    </form>
                <?php endif; ?>
                    <?php if (!$isWishlisted): ?>
                        <form method="POST" action="logged_in_sites/add_to_event_wishlist.php" class="mt-3">
                            <input type="hidden" name="event_id" value="<?= htmlspecialchars($eventId) ?>">
                            <button type="submit" class="btn btn-secondary">Add to Wishlist</button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-success mt-3" disabled>Already in Wishlist</button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-warning">Login to Sign Up or Add to Wishlist</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Display invited people and their status for logged-in users -->
        <?php if (isset($_SESSION['session_token'])): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div style="text-align: center">
                    <h3 style="background-color: #BBB8B2; color: #BC5D2E; border-radius: 5px; width: 50%; padding: 1rem; margin: 0 auto; margin-bottom: 1rem;">Invited People</h3>
                </div>
                <form method="GET" class="mb-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($eventId) ?>">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="accepted" <?= $filterStatus === "accepted" ? "selected" : "" ?>>Accepted</option>
                        <option value="declined" <?= $filterStatus === "declined" ? "selected" : "" ?>>Declined</option>
                        <option value="dontknow" <?= $filterStatus === "dontknow" ? "selected" : "" ?>>Doesn't Know</option>
                    </select>
                </form>
                <?php if (!empty($invitedPeople)): ?>
                <ul class="list-group">
                    <?php foreach ($invitedPeople as $person): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong><?= htmlspecialchars($person['firstname'] . ' ' . $person['lastname']) ?></strong>
                                    </span>
                            <span class="badge bg-secondary"><?= htmlspecialchars($person['status'] ?? "pending") ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                    <p>No invited people found.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Comment Section -->
        <?php if ($canComment): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h3>Comments</h3>
                    <form method="POST" action="add_comment.php">
                        <input type="hidden" name="event_id"   value="<?= htmlspecialchars($eventId) ?>">
                        <div class="mb-3">
                            <textarea name="comment_text" class="form-control" placeholder="Write your comment..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rating">Rate the Event:</label>
                            <select name="rating" class="form-select" required>
                                <option value="1">1 – Poor</option>
                                <option value="2">2 – Fair</option>
                                <option value="3">3 – Good</option>
                                <option value="4">4 – Very Good</option>
                                <option value="5">5 – Excellent</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                    <?php elseif (isset($_SESSION['session_token']) && $event['comments_enabled']): ?>
                        <p class="text-white mt-5">Either commenting is disabled, the event hasn’t ended yet, or you’ve already left one.</p>
                    <?php endif; ?>

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
                                        <form method="GET" action="delete_comment.php" class="d-inline">
                                            <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id_comment']) ?>">
                                            <input type="hidden" name="event_id"   value="<?= htmlspecialchars($eventId) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No comments yet.</p>
                    <?php endif; ?>
                </div>
            </div>
    </div>
    <?php include_once 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
