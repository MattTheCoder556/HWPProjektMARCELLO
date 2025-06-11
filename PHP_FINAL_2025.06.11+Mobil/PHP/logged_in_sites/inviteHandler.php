<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../functions.php';
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);

header("Content-Type: application/json");

try {
    // Parse JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate input
    if (empty($data["event_id"]) || empty($data["email"])) {
        echo json_encode(["error" => "Event ID and email are required."]);
        exit;
    }

    $eventId = $data["event_id"];
    $email = $data["email"];
    $includeWishlist = $data['include_wishlist'] ?? false;
    $wishlistData = $data['wishlist_data'] ?? null;
    $template_data = $data["template_data"];
    $username = $_SESSION["username"];

    // Database connection
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Check for template data
    $requiredFields = [
        'event_name' => "Event name is required.",
        'event_description' => "Event description is required.",
        'background_color' => "Background color is required.",
        'color' => "Font color is required."
    ];

    foreach ($requiredFields as $field => $errorMessage) {
        if (empty($template_data[$field])) {
            echo json_encode(["error" => $errorMessage]);
            exit;
        }
    }

    // Check if the email exists
    $stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = :email");
    $stmt->execute([":email" => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(["error" => "No user found with the provided email."]);
        exit;
    }

    $userId = $user["id_user"];

    if ($username === $email) {
        echo json_encode(["error" => "You cannot invite yourself."]);
        exit;
    }

    // Check if the invited user is the owner of the event
    $stmt = $pdo->prepare("SELECT owner FROM events WHERE id_event = :event_id");
    $stmt->execute([":event_id" => $eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        echo json_encode(["error" => "Event not found."]);
        exit;
    }

    if ($event["owner"] == $userId) {
        echo json_encode(["error" => "You cannot invite the owner of the event."]);
        exit;
    }


    // Check if the user is already invited
    $stmt = $pdo->prepare("SELECT * FROM event_invites WHERE id_event = :event_id AND id_user = :user_id");
    $stmt->execute([":event_id" => $eventId, ":user_id" => $userId]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "User is already invited to this event."]);
        exit;
    }

    // Fetch event start time
    $stmt = $pdo->prepare("SELECT start_date FROM events WHERE id_event = :event_id");
    $stmt->execute([":event_id" => $eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        echo json_encode(["error" => "Event not found."]);
        exit;
    }

    // Use event start time as the expiration date
    $inviteExpire = $event["start_date"];

    // Generate an invite token
    $inviteToken = bin2hex(random_bytes(16));

    // Insert invite into the database
    $stmt = $pdo->prepare("
        INSERT INTO event_invites (id_event, id_user, invited_by, status, invite_token, invite_expire) 
        VALUES (:event_id, :user_id, :username,'pending', :invite_token, :invite_expire)
    ");
    $stmt->execute([
        ":event_id" => $eventId,
        ":user_id" => $userId,
        ":username" => $username,
        ":invite_token" => $inviteToken,
        ":invite_expire" => $inviteExpire,
    ]);

    // Prepare wishlist data for email
    $wishlistItems = [];
    if ($includeWishlist) {
        if ($wishlistData && !empty($wishlistData['wishes'])) {
            $wishlistItems = $wishlistData['wishes'];
        } else {
            $stmt = $pdo->prepare("SELECT wishes FROM gift_wishlists WHERE id_event = :event_id");
            $stmt->execute([':event_id' => $eventId]);
            $wishlist = $stmt->fetchColumn();
            $wishlistItems = $wishlist ? json_decode($wishlist, true) : [];
        }
    }

// JSON formátumban küldjük, nem HTML-ként
    $wishlistHtml = json_encode($wishlistItems);

    // Send the invitation email
    try {
        sendInviteEmail(
            $email,
            $inviteToken,
            $username,
            $wishlistHtml,
            $template_data
        );

        echo json_encode(["success" => "Invite sent successfully."]);
    } catch (Exception $e) {
        // Delete the invite if email sending fails
        $pdo->prepare("DELETE FROM event_invites WHERE invite_token = ?")->execute([$inviteToken]);
        echo json_encode(["error" => "Failed to send invitation email: " . $e->getMessage()]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}
