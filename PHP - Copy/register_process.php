<?php
require "config.php";
require "functions.php";

// Set the content type to JSON for API response
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Fetch input data from POST or JSON
$data = json_decode(file_get_contents('php://input'), true);
$data = $data ?: $_POST; // Use $_POST as a fallback for form submissions

// Ensure all fields are present
$requiredFields = ['firstname', 'lastname', 'username', 'phone', 'password'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode([
            'success' => false,
            'message' => "Missing or empty field: $field"
        ]);
        exit;
    }
}

// Sanitize inputs
$firstname = $data['firstname'];
$lastname = $data['lastname'];
$username = $data['username'];
$phone = $data['phone'];
$password = $data['password'];

try {
    // Call the registerUser function
    registerUser($firstname, $lastname, $username, $phone, $password, $dbHost, $dbName, $dbUser, $dbPass);
    echo json_encode(['success' => true, 'message' => 'User registered successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
