<?php
require "config.php";
require "functions.php";

$baseURL1 = "/HWPProjektMARCELLO/PHP/index.php"; //Gabor URL
$baseURL2 = "/HWP_2024/HWPProjektMARCELLO/PHP/index.php"; //Mate URL
$baseURL2Admin = "/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/admin/admin_dashboard.php"; //Mate admin dashboard

// Set headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Debugging (optional)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detect if it's a JSON request
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isJsonRequest = strpos($contentType, 'application/json') !== false;

// Parse credentials
if ($isJsonRequest) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
} else {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
}

// Validate input
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Both fields are required to login.']);
    exit();
}

try {
    // Attempt regular user login
    $result = loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        $userId = $result['user_id'];

        if ($isJsonRequest) {
            echo json_encode([
                'success' => true,
                'userId' => $userId,
                'message' => 'Login successful.'
            ]);
        } else {
            session_start();
            $_SESSION['user_id'] = $userId;
            header("Location: " . $baseURL2);
        }
        exit();
    }

    // If user login failed, try admin login
    $result = loginAdmin($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        $adminId = $result['admin_id'];

        if ($isJsonRequest) {
            echo json_encode([
                'success' => true,
                'adminId' => $adminId,
                'message' => 'Admin login successful.'
            ]);
        } else {
            session_start();
            $_SESSION['admin_id'] = $adminId;
            header("Location: " . $baseURL2Admin);
        }
        exit();
    }

    // If both failed
    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Invalid credentials.']);
    exit();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit();
}
