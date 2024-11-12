<?php
session_start();
//$photo = $_POST['photo'];
$title = $_POST['title'];
$number = $_POST['number'];
$type = $_POST['type'];
$other = $_POST['other'];
$public = $_POST['public'];

var_dump( $title, $number, $type, $other, $public);

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo = $_FILES['photo'];
    
    // Check if the file is actually an image (optional)
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = "../assets/eventPictures/";

    $fileName = basename($_FILES["photo"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
            echo "The file " . htmlspecialchars($fileName) . " has been uploaded successfully.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
else {
    echo "No file uploaded.";
}
?>