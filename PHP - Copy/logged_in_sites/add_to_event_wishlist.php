<?php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['session_token'])) {
    header("Location: ../login.php");
    exit;
}

$eventId = $_POST['event_id'] ?? null;

if (!$eventId) {
    die("Event ID is missing!");
}

try {
    $sessionToken = $_SESSION['session_token'];
    $stmt = $pdo->prepare("SELECT id_user FROM session_tokens WHERE token = :session_token AND expiry_date > NOW()");
    $stmt->execute([':session_token' => $sessionToken]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Invalid session or session expired');
    }

    $userId = $user['id_user'];

    // Add the event to the wishlist
    $stmt = $pdo->prepare("INSERT INTO event_wishlists (id_event, id_user, status) VALUES (:event_id, :user_id, 'wishlisted')");
    $stmt->execute([':event_id' => $eventId, ':user_id' => $userId]);

    header("Location: ../eventDetails.php?id=" . $eventId . "&wishlist_success=1");
    exit;

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
