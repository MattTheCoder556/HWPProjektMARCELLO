<?php
require "config.php";
require "functions.php";

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read the input data
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
} else {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
}




// Validate input
if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Both fields are required to login.'
    ]);
    exit();
}

try {
    // Attempt login
    $result = loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
        ]);
        if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            header('Location: ./index.php');
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Invalid credentials.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

exit();