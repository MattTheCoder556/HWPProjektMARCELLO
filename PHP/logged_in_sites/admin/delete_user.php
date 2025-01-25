<?php
session_start();

// Check if the user is logged in and is an admin
/*if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}*/

require_once "../../config.php";

if (isset($_POST['id_user'])) {
    $id_user = (int)$_POST['id_user'];

    // Start transaction to delete the user
    $pdo->beginTransaction();

    try {
        // Step 1: Nullify registration_token (or you can set it to an empty string if needed)
        $sql1 = "UPDATE users SET registration_token = NULL WHERE id_user = :id_user";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt1->execute();

        // Step 2: Now delete the user from the users table
        $sql2 = "DELETE FROM users WHERE id_user = :id_user";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt2->execute();

        // Commit transaction
        $pdo->commit();

        // Redirect back to the users page
        header('Location: admin_users.php');
        exit;
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
