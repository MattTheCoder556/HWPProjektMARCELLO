<?php
// Update user profile

require_once '../config.php';
require_once '../functions.php';
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);
try
{
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    die("Database connection failed: " . $e->getMessage());
}

$username = $_SESSION['username'];

// Fetch `id_user` for the logged-in username
$stmtUser = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
$stmtUser->bindValue(':username', $username, PDO::PARAM_STR);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user)
{
    die("User not found.");
}

$userId = $user['id_user'];

// Maximum length constraints
$maxLengths = [
    'firstname' => 40,
    'lastname' => 40,
    'username' => 50,
];

$firstname = htmlspecialchars(trim($_POST['firstname']));
$lastname = htmlspecialchars(trim($_POST['lastname']));
$newUsername = htmlspecialchars(trim($_POST['username']));
$phone = htmlspecialchars(trim($_POST['phone']));

// Input validation
if (strlen($firstname) > $maxLengths['firstname']) {
    die("First name cannot exceed " . $maxLengths['firstname'] . " characters.");
}

if (strlen($lastname) > $maxLengths['lastname']) {
    die("Last name cannot exceed " . $maxLengths['lastname'] . " characters.");
}

if (!filter_var($newUsername, FILTER_VALIDATE_EMAIL) || strlen($newUsername) > $maxLengths['username']) {
    die("Please enter a valid email address (maximum " . $maxLengths['username'] . " characters).");
}

if (!preg_match('/^\d{1,3} \d{7,12}$/', $phone)) {
    die("Please enter a valid phone number with format: (country code)[space](phone number), e.g.: 123 1234567890.");
}

// Check if the new username is already taken by another user
$stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = :newUsername AND id_user != :id_user");
$stmt->bindValue(':newUsername', $newUsername, PDO::PARAM_STR);
$stmt->bindValue(':id_user', $userId, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    die("The username (email) is already taken by another user.");
}

// Update query
$stmt = $pdo->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, username = :newUsername, phone = :phone WHERE id_user = :id_user");
$stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
$stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
$stmt->bindValue(':newUsername', $newUsername, PDO::PARAM_STR);
$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
$stmt->bindValue(':id_user', $userId, PDO::PARAM_INT);
if ($stmt->execute())
{
    echo "Your credentials were updated successfully!";
    $_SESSION['username'] = $newUsername;
}
else
{
    echo "Error during updating credentials!";
}
?>