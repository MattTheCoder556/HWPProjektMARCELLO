<?php
require "config.php";
require "functions.php";

$baseURL1 = "/HWPProjektMARCELLO/PHP/index.php"; //Gabor URL
$baseURL2 = "/HWP_2024/HWPProjektMARCELLO/PHP/index.php"; //Mate URL
$baseURL2Admin = "/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/admin/admin_dashboard.php"; //Mate admin dashboard

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
        showPopupAndRedirect('Both fields are required to login.', $baseURL2);
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
            session_start();
            $_SESSION['user_id'] = $result['user_id'];
            header("Location: " . $baseURL2);
        }
        exit();
    }

    // Try admin login
    $result = loginAdmin($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        if ($isJsonRequest) {
            echo json_encode(['success' => true, 'adminId' => $result['admin_id'], 'message' => 'Admin login successful.']);
        } else {
            session_start();
            $_SESSION['admin_id'] = $result['admin_id'];
            header("Location: " . $baseURL2Admin);
        }
        exit();
    }

    // If both failed
    if ($isJsonRequest) {
        echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Invalid credentials.']);
    } else {
        showPopupAndRedirect($result['message'] ?? 'Incorrect username or password.', $baseURL2);
    }
    exit();
} catch (Exception $e) {
    if ($isJsonRequest) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    } else {
        showPopupAndRedirect('An error occurred: ' . $e->getMessage(), $baseURL2);
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
