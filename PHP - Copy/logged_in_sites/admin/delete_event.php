<?php
session_start();
require_once "../../config.php";
require '../../../vendor/autoload.php'; // path to Composer's autoload.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if event ID is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_event'])) {
    $id_event = intval($_POST['id_event']);

    // Fetch event and owner info before deleting
    $stmt = $pdo->prepare("
        SELECT e.event_name, u.id_user, u.username AS username
        FROM events e
        JOIN users u ON e.owner = u.id_user
        WHERE e.id_event = :id_event
    ");
    $stmt->execute(['id_event' => $id_event]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        // Delete the event
        $deleteStmt = $pdo->prepare("DELETE FROM events WHERE id_event = :id_event");
        $deleteStmt->execute(['id_event' => $id_event]);

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'd4a04c8e5deb9e'; // Your SMTP username
            $mail->Password = 'bde0a6f4e281eb';       // Your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // or PHPMailer::ENCRYPTION_SMTPS
            $mail->Port = 587; // 587 for TLS, 465 for SSL

            // Recipients
            $mail->setFrom('mmm@yourdomain.com', 'Event Management');
            $mail->addAddress($event['username']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Your event '{$event['event_name']}' has been deleted";
            $mail->Body    = "
                <p>Dear {$event['username']},</p>
                <p>We would like to inform you that your event titled <strong>{$event['event_name']}</strong> has been deleted by an administrator.</p>
                <p>If you believe this was a mistake, please contact support.</p>
                <p>Regards,<br>Event Management Team</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
    }
}

// Redirect back to admin event page
header("Location: admin_events.php");
exit();
