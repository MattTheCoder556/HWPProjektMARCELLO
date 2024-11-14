<?php
session_start();

require 'config.php';

$title = $_POST['title'];
$number = $_POST['number'];
$type = $_POST['type'];
$other = $_POST['other'];
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];
$public = $_POST['public'];
$eventCity = $_POST['eventCity'];
$eventStreet = $_POST['eventStreet'];
$eventHouse = $_POST['eventHouse'];
$eventDesc = $_POST['eventDesc'];
$currentDate = date('Y-m-d');
$eventAddress = $eventCity .', '. $eventStreet .', '. $eventHouse;
$eventType = ($type === 'other' && !empty($other)) ? $other : $type;
$user = 2;

var_dump( $title, $number, $type, $other, $public, $startDate, $endDate, $currentDate, $eventAddress);



if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo = $_FILES['photo'];
    
    $fileType = mime_content_type($photo['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['message'] = "Please upload a valid image file (JPG, PNG, or GIF).";
        header("Location: event.php");
        exit;
    }
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Save the file with a unique name
    $fileName = uniqid() . "-" . basename($photo['name']);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($photo['tmp_name'], $targetFilePath)) {
        $imagePath = $targetFilePath;
    } else {
        $_SESSION['message'] = "Failed to upload image.";
        header("Location: index.php");
        exit;
    }
} else {
    $_SESSION['message'] = "Please insert a picture of your event.";
    header("Location: event.php");
    exit;
}

if(empty($title)){
    $_SESSION['message2'] = "Please write the name/title of your event.";
    header("Location: event.php");
}

if(empty($number)){
    $_SESSION['message3'] = "Please give the number of attendees.";
    header("Location: event.php");
}

if(empty($type)){
    $_SESSION['message4'] = "Please give the type of your event.";
    header("Location: event.php");
}

if ($startDate < $currentDate) {
    $_SESSION['message7'] = "The start date cannot be in the past.";
    header("Location: event.php");
    exit; 
}

if ($endDate < $startDate) {
    $_SESSION['message8'] = "The end date cannot be before the start date.";
    header("Location: event.php");
    exit; 
}

try {
    // Check if the user exists in the users table
    $stmt = $pdo->prepare("SELECT id_user FROM users WHERE id_user = :user");
    $stmt->execute([':user' => $user]);
    $userExists = $stmt->fetchColumn();

    // If the user does not exist, return an error message
    if (!$userExists) {
        $_SESSION['message'] = "User does not exist!";
        echo "Error: User does not exist!";
        exit; // Stop further execution
    }

    // If the user exists, proceed with inserting the event data
    $stmt = $pdo->prepare("INSERT INTO events (event_pic, event_name, attendees, event_type, start_date, end_date, description, place, owner)
                           VALUES (:eventPic, :eventName, :attendees, :eventType, :startDate, :endDate, :description, :place, :owner)");

    $stmt->execute([
        ':eventPic' => $imagePath,
        ':eventName' => $title,
        ':attendees' => $number,
        ':eventType' => $eventType,
        ':startDate' => $startDate,
        ':endDate' => $endDate,
        ':description' => $eventDesc,
        ':place' => $eventAddress,
        ':owner' => $user // Ensure this is a valid user ID
    ]);

    $_SESSION['message'] = "Event created successfully!";
    echo 'Successfully uploaded!';
} catch (PDOException $e) {
    // Catch any PDO errors and show them
    $_SESSION['message'] = "Error: " . $e->getMessage();
    echo 'Error: ' . $e->getMessage();
}

?>