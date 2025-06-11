<?php
session_start();

// Check if the user is logged in and is an admin
/*if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}*/

require_once "../../config.php";

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id_user = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'ban') {
        $sql = "UPDATE users SET is_banned = 1 WHERE id_user = :id_user";
    } elseif ($action === 'unban') {
        $sql = "UPDATE users SET is_banned = 0 WHERE id_user = :id_user";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to the users page
    header('Location: admin_users.php');
    exit;
}
?>
