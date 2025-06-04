<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    // Ellenőrizzük a session-t
    session_start();
    if (!isset($_SESSION['session_token'])) {
        throw new Exception('Unauthorized access');
    }

    // Felhasználó ID lekérése a session tokenből
    $stmt = $pdo->prepare("SELECT id_user FROM session_tokens WHERE token = :token");
    $stmt->execute([':token' => $_SESSION['session_token']]);
    $user = $stmt->fetch();
    if (!$user) {
        throw new Exception('Invalid session');
    }
    $userId = $user['id_user'];

    // Művelet típusának meghatározása
    $action = $_REQUEST['action'] ?? 'get_reminders';

    switch ($action) {
        case 'get_reminders':
            // Lekérdezés: Aktuális emlékeztetők
            $eventId = $_GET['event_id'] ?? null;
            if (!$eventId) {
                throw new Exception('Event ID is required');
            }

            $stmt = $pdo->prepare("
                SELECT id_reminder, title, content, reminder_time 
                FROM event_reminders 
                WHERE id_event = :event_id AND id_user = :user_id AND reminder_time > NOW()
                ORDER BY reminder_time ASC
            ");
            $stmt->execute([':event_id' => $eventId, ':user_id' => $userId]);
            $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'reminders' => $reminders]);
            break;

        case 'add_reminder':
            // Hozzáadás: Új emlékeztető
            $eventId = $_POST['event_id'] ?? null;
            $title = $_POST['title'] ?? null;
            $content = $_POST['content'] ?? '';
            $reminderTime = $_POST['reminder_time'] ?? null;

            if (!$eventId || !$title || !$reminderTime) {
                throw new Exception('Missing required fields');
            }

            $stmt = $pdo->prepare("
                INSERT INTO event_reminders 
                (id_event, id_user, title, content, reminder_time) 
                VALUES (:event_id, :user_id, :title, :content, :reminder_time)
            ");
            $stmt->execute([
                ':event_id' => $eventId,
                ':user_id' => $userId,
                ':title' => $title,
                ':content' => $content,
                ':reminder_time' => $reminderTime
            ]);

            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'delete_reminder':
            // Törlés: Meglévő emlékeztető
            $reminderId = $_POST['reminder_id'] ?? null;
            if (!$reminderId) {
                throw new Exception('Reminder ID is required');
            }

            $stmt = $pdo->prepare("
                DELETE FROM event_reminders 
                WHERE id_reminder = :reminder_id AND id_user = :user_id
            ");
            $stmt->execute([':reminder_id' => $reminderId, ':user_id' => $userId]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Reminder not found or access denied');
            }

            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}