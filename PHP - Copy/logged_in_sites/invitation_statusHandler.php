<?php
require_once "../config.php";
require_once "../functions.php";
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);

if($_SERVER["REQUEST_METHOD"] == "GET")
{
    $action = $_GET["action"];
    $token = $_GET["token"] ?? "";

    if ($token == "" || $action == "")
    {
        echo json_encode(["error" => "Missing or invalid request parameters."]);
        exit();
    }

    // Token validity
    if (!isValidToken($pdo, $token))
    {
        echo json_encode(["error" => "Invalid or expired token."]);
        exit();
    }

    // Action handling based on user selection
    switch ($action)
    {
        case "accept":
            acceptInvitation($pdo, $token);
            echo json_encode(["success" => "Invitation accepted!"]);
            break;

        case "decline":
            declineInvitation($pdo, $token);
            echo json_encode(["success" => "Invitation declined!"]);
            break;

        case "dontknow":
            dontKnowInvitation($pdo, $token);
            echo json_encode(["success" => "You selected 'Don't know'."]);
            break;

        default:
            echo json_encode(["error" => "Unknown action."]);
            break;
    }
}
else
{
    echo json_encode(["error" => "Invalid request method."]);
}

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