<?php

require_once 'config.php';

$eventId = $_GET['event_id'] ?? null;
$userId = $_GET['user_id'] ?? null;

if (!$eventId || !$userId) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.message, c.sent_at, u.username
    FROM chat_messages c
    JOIN users u ON c.sender_id = u.id_user
    WHERE c.event_id = :event_id
    ORDER BY c.sent_at ASC
");
$stmt->execute([':event_id' => $eventId]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($messages);
?>
