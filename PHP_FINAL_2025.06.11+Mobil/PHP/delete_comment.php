<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['session_token'])) {
    header('Location: login.php');
    exit;
}

try {
    // Retrieve the logged-in user's ID
    $sessionToken = $_SESSION['session_token'];
    $stmt = $pdo->prepare("SELECT id_user FROM session_tokens WHERE token = :session_token AND expiry_date > NOW()");
    $stmt->execute([':session_token' => $sessionToken]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Invalid session or session expired.');
    }

    $userId = $user['id_user'];

    // Get the comment ID from POST
    $commentId = $_GET['comment_id'] ?? null;

    if (!$commentId) {
        throw new Exception('Comment ID is required.');
    }

    // Check if the comment belongs to the user
    $stmt = $pdo->prepare("SELECT user_id FROM event_comments WHERE id_comment = :comment_id");
    $stmt->execute([':comment_id' => $commentId]);
    $comment = $stmt->fetch();

    if (!$comment || $comment['user_id'] != $userId) {
        throw new Exception('You can only delete your own comments.');
    }

    // Delete the comment
    $stmt = $pdo->prepare("DELETE FROM event_comments WHERE id_comment = :comment_id");
    $stmt->execute([':comment_id' => $commentId]);

    // Redirect back to the event details page
    header('Location: eventDetails.php?id=' . $_GET['event_id']);
    exit;
} catch (Exception $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}
