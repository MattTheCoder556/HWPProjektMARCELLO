<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    $commentId = $_POST['comment_id'];

    try {
        session_start();
        $sessionToken = $_SESSION['session_token'] ?? null;

        if ($sessionToken) {
            $stmt = $pdo->prepare("SELECT id_user FROM session_tokens WHERE token = :session_token AND expiry_date > NOW()");
            $stmt->execute([':session_token' => $sessionToken]);
            $user = $stmt->fetch();

            if ($user) {
                $userId = $user['id_user'];

                // Delete comment if the logged-in user is the author
                $stmt = $pdo->prepare("DELETE FROM event_comments WHERE id_comment = :comment_id AND user_id = :user_id");
                $stmt->execute([':comment_id' => $commentId, ':user_id' => $userId]);

                if ($stmt->rowCount() > 0) {
                    header("Location: eventDetails.php?id=" . $_POST['event_id']);
                    exit;
                } else {
                    throw new Exception("You can only delete your own comments.");
                }
            }
        }
    } catch (Exception $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>
