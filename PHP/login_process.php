<?php
require "config.php";
require "functions.php";

// Allow cross-origin requests (for React Native and others)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json'); // Always send JSON response

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to manage headers and content
ob_start();

// Detect if the request is AJAX (i.e., from React Native or other AJAX clients)
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

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
    // Return JSON response for both web and mobile apps
    echo json_encode(['success' => false, 'message' => 'Both fields are required to login.']);
    exit();
}

if ($username === 'admin@mmm.com' && $password === 'admin') {
    // Set session variables for admin login
    $_SESSION['is_admin'] = true;
    $_SESSION['is_admin_u'] = 'admin';

    // Return success response with message (for both AJAX and web)
    echo json_encode(['success' => true, 'message' => 'Admin login successful.']);
    exit();
}

try {
    // Attempt login
    $result = loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        // Retrieve the userId from the result
        $userId = $result['user_id'];

        // Return success response with userId for both AJAX and web
        echo json_encode(['success' => true, 'userId' => $userId]);
        exit();
    } else {
        // Return failure response with message for both AJAX and web
        echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Invalid credentials.']);
        exit();
    }
} catch (Exception $e) {
    // Handle exception and return a JSON error response
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit();
}
