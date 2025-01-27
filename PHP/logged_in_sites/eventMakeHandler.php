<?php
//session_start();

require '../config.php';
require '../functions.php';

/*if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "You must be logged in to create an event.";
    header("Location: login.php");
    exit;
}*/

$title = $_POST['title'];
$number = $_POST['number'];
$type = $_POST['type'];
$other = $_POST['other'];
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];
$eventCity = $_POST['eventCity'];
$eventStreet = $_POST['eventStreet'];
$eventHouse = $_POST['eventHouse'];
$eventDesc = $_POST['eventDesc'];
$currentDate = date('Y-m-d');
$eventAddress = $eventCity .', '. $eventStreet .', '. $eventHouse;
$eventType = ($type === 'other' && !empty($other)) ? $other : $type;
$user = $_SESSION['username']; // Assuming user is logged in and session contains username

// Check if the 'public' checkbox was checked and set the value accordingly
$public = isset($_POST['public']) ? 1 : 0;
$commentsEnabled = isset($_POST['comments_enabled']) ? 1 : 0;


// Fetch the user_id for the current username
$stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
$stmt->execute([':username' => $user]);
$userID = $stmt->fetchColumn();

// Check if user exists
if (!$userID) {
    $_SESSION['message'] = "User does not exist!";
    header("Location: eventMaker.php");
    exit;
}

// Check if file upload is valid and process the image
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo = $_FILES['photo'];
    
    $fileType = mime_content_type($photo['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['message'] = "Please upload a valid image file (JPG, PNG, or GIF).";
        header("Location: eventMaker.php");
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
        header("Location: ../index.php");
        exit;
    }
} else {
    $_SESSION['message'] = "Please insert a picture of your event.";
    header("Location: eventMaker.php");
    exit;
}

if(empty($title)){
    $_SESSION['message2'] = "Please write the name/title of your event.";
    header("Location: eventMaker.php");
}

if(empty($number)){
    $_SESSION['message3'] = "Please give the number of attendees.";
    header("Location: eventMaker.php");
}

if(empty($type)){
    $_SESSION['message4'] = "Please give the type of your event.";
    header("Location: eventMaker.php");
}

if ($startDate < $currentDate) {
    $_SESSION['message7'] = "The start date cannot be in the past.";
    header("Location: eventMaker.php");
    exit; 
}

if ($endDate < $startDate) {
    $_SESSION['message8'] = "The end date cannot be before the start date.";
    header("Location: eventMaker.php");
    exit; 
}

try {
    // Insert event data into the database
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
        ':owner' => $userID,  // Use the user ID as the owner
        ':public' => $public,  // Pass the value of the public checkbox
        ':comments' => $commentsEnabled  // Pass the value of the comments_enabled checkbox
    ]);

    // Retrieve the ID of the newly inserted event
    $eventID = $pdo->lastInsertId();  // This will give the last inserted row ID

    // Store a message indicating success
    $_SESSION['message'] = "Event created successfully!";

    // Redirect to the event details or invitation page
    header('Location: invitationMaker.php?id=' . $eventID); // Redirect to the invitation maker with the event ID
    exit;

} catch (PDOException $e) {
    // Catch any errors and display the message
    $_SESSION['message'] = "Error: " . $e->getMessage();
    echo 'Error: ' . $e->getMessage();
}
?>
