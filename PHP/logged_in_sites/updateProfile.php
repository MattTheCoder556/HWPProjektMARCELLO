<?php
// Update user profile

require_once '../config.php';
require_once '../functions.php';

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

$userId = $user['id_user'];

$firstname = htmlspecialchars($_POST['firstname']);
$lastname = htmlspecialchars($_POST['lastname']);
$newUsername = htmlspecialchars($_POST['username']);
$phone = htmlspecialchars($_POST['phone']);

// Update query
$stmt = $pdo->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, username = :newUsername, phone = :phone WHERE id_user = :id_user");
$stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
$stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
$stmt->bindValue(':newUsername', $username, PDO::PARAM_STR);
$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
$stmt->bindValue(':id_user', $userId, PDO::PARAM_INT);
if ($stmt->execute())
{
    echo "Your credentials were updated successfully!";
}
else
{
    echo "Error during updating credentials!";
}
?>