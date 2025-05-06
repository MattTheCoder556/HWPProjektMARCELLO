<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['session_token'])) {
    header('Location: login.php');
    exit;
}

try
{
    $stmt = $pdo->prepare("
      SELECT u.id_user
      FROM users u
      JOIN session_tokens s ON s.id_user = u.id_user
      WHERE s.token = :token
        AND s.expiry_date > NOW()
    ");
    $stmt->execute([':token' => $_SESSION['session_token']]);
    $me = $stmt->fetch();
    if (!$me) throw new Exception('Invalid session.');

    $userId      = $me['id_user'];
    $eventId     = $_POST['event_id']      ?? null;
    $commentText = trim($_POST['comment_text'] ?? '');
    $rating      = $_POST['rating']        ?? null;

    // Basic form checks
    if (! $eventId || ! $commentText || ! $rating)
    {
        throw new Exception('All fields are required.');
    }

    // Event exists, allows comments, is over
    $stmt = $pdo->prepare("
      SELECT end_date, comments_enabled
      FROM events
      WHERE id_event = :event_id
    ");
    $stmt->execute([':event_id' => $eventId]);
    $event = $stmt->fetch();

    if (! $event) {
        throw new Exception('Event not found.');
    }
    if (! $event['comments_enabled']) {
        throw new Exception('Comments are disabled for this event.');
    }
    if (new DateTime($event['end_date']) > new DateTime) {
        throw new Exception('You can only comment after the event has ended.');
    }

    // One-per-user enforced by DB, but pre-check to give nicer error
    $stmt = $pdo->prepare("
      SELECT COUNT(*) 
      FROM event_comments 
      WHERE event_id = :event_id 
        AND user_id = :user_id
    ");
    $stmt->execute([
        ':event_id' => $eventId,
        ':user_id'  => $userId
    ]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('You have already commented on this event.');
    }

    // Finally insert
    $stmt = $pdo->prepare("
      INSERT INTO event_comments (event_id, user_id, comment_text, rating)
      VALUES (:event_id, :user_id, :comment_text, :rating)
    ");
    $stmt->execute([
        ':event_id'     => $eventId,
        ':user_id'      => $userId,
        ':comment_text' => $commentText,
        ':rating'       => $rating
    ]);

    header('Location: eventDetails.php?id=' . $eventId);
    exit;

} catch (Exception $e) {
    echo '<div class="alert alert-danger m-4">'
        . htmlspecialchars($e->getMessage())
        . '</div>';
}
