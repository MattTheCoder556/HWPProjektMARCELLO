<?php
include_once "../config.php";
include_once "../functions.php";
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);

ob_start();

try {
    $username = $_SESSION['username'];
    $eventId = $_POST['event_id'] ?? null;


    if (!$eventId) {
        throw new Exception("No event ID provided!");
    }

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass, [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);


    $stmtUser = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
    $stmtUser->execute([':username' => $username]);
    $user = $stmtUser->fetch();

    if (!$user) {
        throw new Exception("Invalid user. No matching user found.");
    }

    $userId = $user['id_user'];


    $stmtEventOwner = $pdo->prepare("SELECT owner FROM events WHERE id_event = :event_id");
    $stmtEventOwner->execute([':event_id' => $eventId]);
    $event = $stmtEventOwner->fetch();

    if (!$event) {
        throw new Exception("Event not found.");
    }

    if ($event['owner'] == $userId) {
        throw new Exception("You cannot subscribe to your own event.");
    }


    $stmtSignup = $pdo->prepare("SELECT * FROM event_signups WHERE event_id = :event_id AND user_id = :user_id");
    $stmtSignup->execute([':event_id' => $eventId, ':user_id' => $userId]);

    if ($stmtSignup->fetch()) {
        throw new Exception("You are already signed up for this event!");
    }


    $stmtInsert = $pdo->prepare("INSERT INTO event_signups (event_id, user_id) VALUES (:event_id, :user_id)");
    $stmtInsert->execute([':event_id' => $eventId, ':user_id' => $userId]);

    $message = "You have successfully signed up for the event!";
    $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '../index.php';
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '../index.php';
}

ob_end_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting...</title>
    <script>
        alert("<?= addslashes($message) ?>");
        window.location.href = "<?= $redirectUrl ?>";
    </script>
</head>
<body>
</body>
</html>