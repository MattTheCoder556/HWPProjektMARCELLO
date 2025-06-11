<?php
require '../config.php';
require '../functions.php';

tokenVerify($dbHost, $dbName, $dbUser, $dbPass);
$errors = [];
$baseURL = 'https://mmm.stud.vts.su.ac.rs/PHP/eventDetails.php?id=';
$returnURL = 'eventMaker.php';

$user = $_SESSION['username'] ?? null;
if (!$user) {
    $_SESSION['flash_error'] = "You must be logged in to create an event.";
    header("Location: $returnURL");
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

try {
    $stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
    $stmt->execute([':username' => $user]);
    $userID = $stmt->fetchColumn();

    if (!$userID) {
        $_SESSION['flash_error'] = "User not found.";
        header("Location: $returnURL");
        exit;
    }

    // Validation
    if (empty($title)) $errors[] = "Event title is required.";
    if (empty($number)) $errors[] = "Number of attendees is required.";
    if (empty($type)) $errors[] = "Event type is required.";
    if ($startDate < $currentDate) $errors[] = "Start date cannot be in the past.";
    if ($endDate < $startDate) $errors[] = "End date cannot be before start date.";

    if (!empty($errors)) {
        $_SESSION['flash_error'] = implode("<br>", $errors);
        header("Location: $returnURL");
        exit;
    }

    // Image upload
    $imagePath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo'];
        $fileType = mime_content_type($photo['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['flash_error'] = "Invalid image type.";
            header("Location: $returnURL");
            exit;
        }

        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = uniqid() . "-" . basename($photo['name']);
        $targetFilePath = $targetDir . $fileName;

        if (!move_uploaded_file($photo['tmp_name'], $targetFilePath)) {
            $_SESSION['flash_error'] = "Failed to upload image.";
            header("Location: $returnURL");
            exit;
        }

        $imagePath = $targetFilePath;
    } else {
        $_SESSION['flash_error'] = "Event image is required.";
        header("Location: $returnURL");
        exit;
    }

    // Insert event
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
    header("Location: $baseURL$eventID");
    exit;

} catch (PDOException $e) {
    $_SESSION['flash_error'] = "Database error: " . $e->getMessage();
    header("Location: $returnURL");
    exit;
}
