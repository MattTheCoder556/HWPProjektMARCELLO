<?php
require "config.php";
require "functions.php";

$baseURL = "https://mmm.stud.vts.su.ac.rs/index.php";
$baseURLAdmin = "https://mmm.stud.vts.su.ac.rs/PHP/logged_in_sites/admin/admin_dashboard.php"; //Mate admin dashboard

// Headers for API access
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
//sheader('Content-Type: application/json');

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detect request type
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isJsonRequest = strpos($contentType, 'application/json') !== false;

// Get input
if ($isJsonRequest) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
} else {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
}

// Basic validation
if (empty($username) || empty($password)) {
    if ($isJsonRequest) {
        echo json_encode(['success' => false, 'message' => 'Both fields are required to login.']);
    } else {
        showPopupAndRedirect('Both fields are required to login.', $baseURL);
    }
    exit();
}

try {
    // Try user login
    $result = loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        if ($isJsonRequest) {
            echo json_encode(['success' => true, 'userId' => $result['user_id'], 'message' => 'Login successful.']);
        } else {
            $_SESSION['user_id'] = $result['user_id'];
            header("Location: " . $baseURL);
        }
        exit();
    }

    // Try admin login
    $result = loginAdmin($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        if ($isJsonRequest) {
            echo json_encode(['success' => true, 'adminId' => $result['admin_id'], 'message' => 'Admin login successful.']);
        } else {
            $_SESSION['admin_id'] = $result['admin_id'];
            header("Location: " . $baseURLAdmin);
        }
        exit();
    }

    // If both failed
    if ($isJsonRequest) {
        echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Invalid credentials.']);
    } else {
        showPopupAndRedirect($result['message'] ?? 'Incorrect username or password.', $baseURL);
    }
    exit();
} catch (Exception $e) {
    if ($isJsonRequest) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    } else {
        showPopupAndRedirect('An error occurred: ' . $e->getMessage(), $baseURL);
    }
    exit();
}


// Helper function to show a JS alert and redirect (for browser use)
function showPopupAndRedirect($message, $redirectURL)
{
    echo "<script>
        alert(" . json_encode($message) . ");
        window.location.href = '$redirectURL';
    </script>";
}
