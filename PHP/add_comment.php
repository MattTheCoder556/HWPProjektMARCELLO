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

    // Get the form data
    $eventId = $_POST['event_id'] ?? null;
    $commentText = $_POST['comment_text'] ?? null;
    $rating = $_POST['rating'] ?? null;

    if (!$eventId || !$commentText || !$rating) {
        throw new Exception('All fields are required.');
    }

    // Insert the comment into the database
    $stmt = $pdo->prepare("
        INSERT INTO event_comments (event_id, user_id, comment_text, rating)
        VALUES (:event_id, :user_id, :comment_text, :rating)
    ");
    $stmt->execute([
        ':event_id' => $eventId,
        ':user_id' => $userId,
        ':comment_text' => $commentText,
        ':rating' => $rating
    ]);

    // Redirect back to the event details page
    header('Location: eventDetails.php?id=' . $eventId);
    exit;
} catch (Exception $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}
