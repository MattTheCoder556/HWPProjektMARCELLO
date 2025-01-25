<?php
require_once '../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['session_token'])) {
    header('Location: ../login.php');
    exit;
}

$eventId = $_GET['id'] ?? null;
if (empty($eventId)) {
    die('No event ID provided!');
}

try {
    // Fetch event details
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id_event = :id");
    $stmt->execute([':id' => $eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        throw new Exception('Event not found!');
    }

    // Check if the logged-in user is the creator
    $sessionToken = $_SESSION['session_token'];
    $stmt = $pdo->prepare("SELECT id_user FROM session_tokens WHERE token = :session_token AND expiry_date > NOW()");
    $stmt->execute([':session_token' => $sessionToken]);
    $user = $stmt->fetch();

    if (!$user || $user['id_user'] != $event['owner']) {
        throw new Exception('You are not authorized to edit this event!');
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $description = $_POST['description'] ?? '';
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $place = $_POST['place'] ?? '';

        // Update the event in the database
        $stmt = $pdo->prepare("UPDATE events SET event_name = :name, event_type = :type, description = :description, start_date = :start_date, end_date = :end_date, place = :place WHERE id_event = :id");
        $stmt->execute([
            ':name' => $name,
            ':type' => $type,
            ':description' => $description,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':place' => $place,
            ':id' => $eventId
        ]);

        // Redirect to the event details page
        header('Location: ../eventDetails.php?id=' . $eventId);
        exit;
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
    <title>Edit Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/eventDetails.css">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Edit Event</h1>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($event['event_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Event Type</label>
                <input type="text" class="form-control" id="type" name="type" value="<?= htmlspecialchars($event['event_type']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($event['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($event['start_date']))) ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($event['end_date']))) ?>" required>
            </div>
            <div class="mb-3">
                <label for="place" class="form-label">Location</label>
                <input type="text" class="form-control" id="place" name="place" value="<?= htmlspecialchars($event['place']) ?>" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Update Event</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
