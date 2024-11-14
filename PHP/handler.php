<?php
session_start();
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
$currentDate = date('Y-m-d');
$eventAddress = $eventCity .', '. $eventStreet .', '. $eventHouse;

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

?>