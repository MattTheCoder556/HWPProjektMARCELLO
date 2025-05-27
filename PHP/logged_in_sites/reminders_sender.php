<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$messages = []; // status log
try {
    $pdo = new PDO("mysql:host=localhost;dbname=marcello_v2", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    date_default_timezone_set('Europe/Belgrade');
    $now = new DateTime('now');
    $startWindow = $now->modify('-10 minutes')->format('Y-m-d H:i:s');
    $endWindow = (new DateTime('now'))->modify('+10 minutes')->format('Y-m-d H:i:s');

    $messages[] = "Lekérdezés időpontja: " . date('Y-m-d H:i:s');

    // Emlékeztetők lekérése
    $stmt = $pdo->prepare("
    SELECT er.*, u.username, e.event_name, e.start_date 
    FROM event_reminders er
    JOIN users u ON er.id_user = u.id_user
    JOIN events e ON er.id_event = e.id_event
    WHERE er.email_sent = 0
    AND er.reminder_time BETWEEN :start_window AND :end_window
");

    $stmt->execute([
        ':start_window' => $startWindow,
        ':end_window' => $endWindow
    ]);
    $reminders = $stmt->fetchAll();

    if (empty($reminders)) {
        $messages[] = "Nincs küldendő emlékeztető.";
    }

    foreach ($reminders as $reminder) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'ddd5c19228d753';
            $mail->Password = '138a3f6bfe0c20';
            $mail->SMTPSecure = 'tls';

            $mail->setFrom('mmmarcello@event.org', 'MammaMiaMarcello team');
            $mail->addAddress($reminder['username']);
            $mail->Subject = 'Reminder: ' . $reminder['event_name'];

            $eventTime = new DateTime($reminder['start_date']);
            $htmlContent = "
                <h1>An event is approaching!</h1>
                <p><strong>The event:</strong> {$reminder['event_name']}</p>
                <p><strong>Date:</strong> {$eventTime->format('Y-m-d H:i')}</p>
            ";

            $mail->isHTML(true);
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);

            if ($mail->send()) {
                $messages[] = "Sikeresen elküldve: {$reminder['username']} / {$reminder['event_name']}";
                $stmt = $pdo->prepare("UPDATE event_reminders SET email_sent = 1 WHERE id_reminder = :id_reminder");
                $stmt->execute([':id_reminder' => $reminder['id_reminder']]);
            } else {
                $messages[] = "Sikertelen küldés: {$reminder['username']} ({$mail->ErrorInfo})";
            }
        } catch (Exception $e) {
            $messages[] = "Hiba küldés közben {$reminder['username']}-nek: " . $e->getMessage();
        }
    }
} catch (Exception $e) {
    $messages[] = "Végzetes hiba: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Emlékeztető státusz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center">Emlékeztető E-mail Státusz</h2>
    <ul class="list-group">
        <?php foreach ($messages as $msg): ?>
            <li class="list-group-item"><?= htmlspecialchars($msg) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
