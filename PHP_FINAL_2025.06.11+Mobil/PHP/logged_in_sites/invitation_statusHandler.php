<?php
require_once "../config.php";
require_once "../functions.php";
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);

// Start HTML output
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Invitation Response</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .response-container { 
            max-width: 600px; 
            margin: 5rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        }
        .btn-home {
            background-color: #DE9151;
            border-color: #DE9151;
            color: white;
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
        }
        .btn-home:hover {
            background-color: #BC5D2E;
            border-color: #BC5D2E;
        }
        .status-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="response-container text-center">';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = $_GET["action"] ?? "";
    $token = $_GET["token"] ?? "";

    // Handle missing parameters
    if (empty($token) || empty($action)) {
        echo '
        <div class="status-icon text-danger">❌</div>
        <h2 class="mb-3">Oops! Something is Missing</h2>
        <p class="lead">We could not process your response because some information was missing.</p>
        <p>Please check the invitation link and try again.</p>
        <a href="https://mmm.stud.vts.su.ac.rs/index.php" class="btn btn-home btn-lg mt-3">
            Return to Homepage
        </a>';
    }
    // Handle valid token cases
    else if (isValidToken($pdo, $token)) {
        switch ($action) {
            case "accept":
                acceptInvitation($pdo, $token);
                echo '
                <div class="status-icon text-success">✅</div>
                <h2 class="mb-3">Invitation Accepted!</h2>
                <p class="lead">You\'ve confirmed your attendance. We\'re excited to see you!</p>
                <p>Your event host has been notified of your response.</p>';
                break;

            case "decline":
                declineInvitation($pdo, $token);
                echo '
                <div class="status-icon text-warning">⚠️</div>
                <h2 class="mb-3">Invitation Declined</h2>
                <p class="lead">You\'ve declined this invitation.</p>
                <p>Your event host has been notified of your response.</p>';
                break;

            case "dontknow":
                dontKnowInvitation($pdo, $token);
                echo '
                <div class="status-icon text-info">❔</div>
                <h2 class="mb-3">Maybe Later?</h2>
                <p class="lead">You\'ve selected "Not Sure Yet".</p>
                <p>Your event host has been notified. You can update your response later!</p>';
                break;

            default:
                echo '
                <div class="status-icon text-danger">⚠️</div>
                <h2 class="mb-3">Invalid Action</h2>
                <p class="lead">We didn\'t recognize the action you requested.</p>
                <p>Please check the invitation link and try again.</p>';
                break;
        }
        
        // Add home button for successful responses
        echo '
        <a href="https://mmm.stud.vts.su.ac.rs/index.php" class="btn btn-home btn-lg mt-3">
            Return to Homepage
        </a>';
    }
    // Handle invalid/expired tokens
    else {
        echo '
        <div class="status-icon text-danger">⌛</div>
        <h2 class="mb-3">Invalid or Expired Link</h2>
        <p class="lead">This invitation link is no longer valid.</p>
        <p>This might be because:</p>
        <ul class="text-start">
            <li>The invitation has expired</li>
            <li>You already responded to this event</li>
            <li>The link was modified</li>
        </ul>
        <a href="https://mmm.stud.vts.su.ac.rs/index.php" class="btn btn-home btn-lg mt-3">
            Return to Homepage
        </a>';
    }
}
else {
    echo '
    <div class="status-icon text-danger">❌</div>
    <h2 class="mb-3">Invalid Request</h2>
    <p class="lead">Please use the link provided in your invitation email.</p>
    <a href="https://mmm.stud.vts.su.ac.rs/index.php" class="btn btn-home btn-lg mt-3">
        Return to Homepage
    </a>';
}

echo '
    </div>
</body>
</html>';

function isValidToken($pdo, $token): bool
{
    $stmt = $pdo->prepare("SELECT * FROM event_invites WHERE invite_token = ?");
    $stmt->execute([$token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return (bool)$result;
}

function acceptInvitation($pdo, $token) {
    // Logic to mark the invitation as accepted
    $stmt = $pdo->prepare("UPDATE event_invites SET status = 'accepted' WHERE invite_token = ?");
    $stmt->execute([$token]);
}

function declineInvitation($pdo, $token) {
    // Logic to mark the invitation as declined
    $stmt = $pdo->prepare("UPDATE event_invites SET status = 'declined' WHERE invite_token = ?");
    $stmt->execute([$token]);
}

function dontKnowInvitation($pdo, $token) {
    // Logic to mark the invitation as unsure
    $stmt = $pdo->prepare("UPDATE event_invites SET status = 'dontknow' WHERE invite_token = ?");
    $stmt->execute([$token]);
}