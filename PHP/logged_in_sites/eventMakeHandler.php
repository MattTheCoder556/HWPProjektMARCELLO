<?php
require '../config.php';
require '../functions.php';

header('Content-Type: application/json');

$response = ['success' => false];
$errors = [];

// Simulate a session user for API — remove this if you're using sessions securely
//session_start();
$user = $_SESSION['username'] ?? null;

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
    exit;
}

$title = $_POST['title'] ?? '';
$number = $_POST['number'] ?? '';
$type = $_POST['type'] ?? '';
$other = $_POST['other'] ?? '';
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';
$eventCity = $_POST['eventCity'] ?? '';
$eventStreet = $_POST['eventStreet'] ?? '';
$eventHouse = $_POST['eventHouse'] ?? '';
$eventDesc = $_POST['eventDesc'] ?? '';
$currentDate = date('Y-m-d');
$eventAddress = $eventCity . ', ' . $eventStreet . ', ' . $eventHouse;
$eventType = ($type === 'other' && !empty($other)) ? $other : $type;

$public = isset($_POST['public']) ? 1 : 0;
$commentsEnabled = isset($_POST['comments_enabled']) ? 1 : 0;

// Get user ID
$stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
$stmt->execute([':username' => $user]);
$userID = $stmt->fetchColumn();

if (!$userID) {
    echo json_encode(['success' => false, 'error' => 'User does not exist']);
    exit;
}

// Validation
if (empty($title)) $errors[] = "Event title is required.";
if (empty($number)) $errors[] = "Number of attendees is required.";
if (empty($type)) $errors[] = "Event type is required.";
if ($startDate < $currentDate) $errors[] = "Start date cannot be in the past.";
if ($endDate < $startDate) $errors[] = "End date cannot be before start date.";

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Image upload
$imagePath = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo = $_FILES['photo'];
    $fileType = mime_content_type($photo['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'error' => 'Invalid image type.']);
        exit;
    }

    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $fileName = uniqid() . "-" . basename($photo['name']);
    $targetFilePath = $targetDir . $fileName;

    if (!move_uploaded_file($photo['tmp_name'], $targetFilePath)) {
        echo json_encode(['success' => false, 'error' => 'Failed to upload image.']);
        exit;
    }

    $imagePath = $targetFilePath;
} else {
    echo json_encode(['success' => false, 'error' => 'Event image is required.']);
    exit;
}

try {
    // Insert event (PDO code unchanged)
    $stmt = $pdo->prepare("INSERT INTO events (event_pic, event_name, attendees, event_type, start_date, end_date, description, place, owner, public, comments_enabled)
                           VALUES (:eventPic, :eventName, :attendees, :eventType, :startDate, :endDate, :description, :place, :owner, :public, :comments)");

    $stmt->execute([
        ':eventPic' => $imagePath,
        ':eventName' => $title,
        ':attendees' => $number,
        ':eventType' => $eventType,
        ':startDate' => $startDate,
        ':endDate' => $endDate,
        ':description' => $eventDesc,
        ':place' => $eventAddress,
        ':owner' => $userID,
        ':public' => $public,
        ':comments' => $commentsEnabled
    ]);

    $eventID = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Event created successfully.',
        'event_id' => $eventID,
        'image' => $imagePath
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}
