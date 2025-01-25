<?php
session_start();

// Check if the user is logged in and is an admin
/*if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}*/

require_once "../../config.php";

if (isset($_POST['id_event'])) {
    $id_event = (int)$_POST['id_event'];

    // Start transaction to delete the event
    $pdo->beginTransaction();

    try {
        // Delete the event from the events table
        $sql1 = "DELETE FROM events WHERE id_event = :id_event";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->bindParam(':id_event', $id_event, PDO::PARAM_INT);
        $stmt1->execute();

        // Commit transaction
        $pdo->commit();

        // Redirect back to the events page
        header('Location: admin_events.php');
        exit;
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
