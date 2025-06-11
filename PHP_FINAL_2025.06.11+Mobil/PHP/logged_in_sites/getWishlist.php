<?php
require_once '../config.php';
require_once '../functions.php';

header('Content-Type: application/json');

if (!isset($_GET['event_id'])) {
    echo json_encode(['error' => 'Event ID is required']);
    exit();
}

$eventId = (int)$_GET['event_id'];

try {
    $sql = "SELECT wishes FROM gift_wishlists WHERE id_event = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$eventId]);
    $wishlist = $stmt->fetch();

    if ($wishlist) {
        echo json_encode([
            'success' => true,
            'wishes' => json_decode($wishlist['wishes'], true) ?: []
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No wishlist found for this event'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>