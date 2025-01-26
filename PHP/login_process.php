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

// Start output buffering to manage headers and content
ob_start();

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
    displayErrorAndExit("Both fields are required to login.");
}

if ($username === 'admin@mmm.com' && $password === 'admin') {
    // Set session variables for admin login
    $_SESSION['is_admin'] = true;
    $_SESSION['is_admin_u'] = 'admin';
    
    // Redirect to admin page
    header('Location: logged_in_sites/admin/admin_dashboard.php');
    exit;
}

try {
    // Attempt login
    $result = loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        // Redirect if login is successful
        header('Location: ./index.php');
        exit;
    } else {
        displayErrorAndExit($result['message'] ?? 'Invalid credentials.');
    }
} catch (Exception $e) {
    displayErrorAndExit('An error occurred: ' . $e->getMessage());
}

// Function to display an error message as a popup
function displayErrorAndExit($errorMessage) {
    ob_clean(); // Clear any output
    echo "<script>
        alert('" . addslashes($errorMessage) . "');
        window.history.back(); // Go back to the previous page
    </script>";
    exit();
}
?>
