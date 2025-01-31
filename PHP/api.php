<?php
header('Content-Type: application/json');

/*
    Important: Replace * with specific domains (e.g., http://localhost:63342) in production for security purposes.
    II
    II
    ˇˇ
 */
header('Access-Control-Allow-Origin: *'); // Allows requests from any origin

header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Allowed request methods
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allowed headers
header('Access-Control-Allow-Credentials: true'); // If credentials (cookies) are required

session_start();
include 'config.php';

try {
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Determine the API action from the request
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getUserId':
        getUserId($pdo);
        break;

    case 'getEvents':
        getEvents($pdo);
        break;
    
    case 'getEvent':
        getEvent($pdo);
        break;
        
    case 'isUserSignedUp':
        isUserSignedUp($pdo);
        break;

    case 'getUserProfile':
        getUserProfile($pdo);
        break;

    case 'getInvites':
        getInvites($pdo);
        break;

    case 'updateInvite':
        updateInvite($pdo);
        break;

    case 'deleteInvite':
        deleteInvite($pdo);
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid API action"]);
        break;
}

/**
 * Fetch user ID based on username and session token.
 */
function getUserId($pdo) {
    if (!isset($_GET['username'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing 'username' parameter"]);
        return;
    }

    if (!isset($_SESSION['session_token'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing 'session_token' in session"]);
        return;
    }

    $username = $_GET['username'];
    $sessionToken = $_SESSION['session_token'];  // Session token should be in session, not GET.

    // Validate the session token by checking in the session_tokens table
    $stmt = $pdo->prepare("
        SELECT u.id_user 
        FROM users u
        JOIN session_tokens s ON u.id_user = s.id_user
        WHERE u.username = :username AND s.token = :session_token AND s.expiry_date > NOW()
    ");
    $stmt->execute([ ':username' => $username, ':session_token' => $sessionToken ]);
    $user = $stmt->fetch();

    if ($user) {
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "User not found or session token expired"]);
    }
}


/**
 * Fetch events based on user ID and optional search term.
 */
function getEvents($pdo) {
    if (!isset($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing 'user_id' parameter"]);
        return;
    }

    $userId = $_GET['user_id'];
    $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;

    try {
        if ($searchTerm) {
            $stmt = $pdo->prepare("
                SELECT * FROM events 
                WHERE public = 1 
                AND end_date >= NOW() 
                AND owner != :user_id 
                AND (
                    event_name LIKE :search 
                    OR event_type LIKE :search 
                    OR description LIKE :search 
                    OR place LIKE :search
                ) 
                ORDER BY start_date DESC
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':search' => $searchTerm,
            ]);
        } else {
            $stmt = $pdo->prepare("
                SELECT * FROM events 
                WHERE public = 1 
                AND end_date >= NOW() 
                AND owner != :user_id 
                ORDER BY start_date DESC
            ");
            $stmt->execute([':user_id' => $userId]);
        }

        $events = $stmt->fetchAll();
        echo json_encode($events);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch events"]);
    }
}

/**
 * Fetch event details by event ID.
 */
function getEvent($pdo) {
    if (!isset($_GET['id'])) {
        echo json_encode(["error" => "Event ID is required"]);
        exit;
    }
    
    $eventId = $_GET['id'];

    try {
        // Fetch event details
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id_event = :id AND public = 1");
        $stmt->execute([':id' => $eventId]);
        $event = $stmt->fetch();

        if ($event) {
            echo json_encode($event);
        } else {
            echo json_encode(["error" => "Event not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to fetch event details"]);
    }
}

/**
 * Check if the user is signed up for the event.
 */
function isUserSignedUp($pdo) {
    if (!isset($_GET['event_id']) || !isset($_GET['user_id'])) {
        echo json_encode(["error" => "Event ID and User ID are required"]);
        exit;
    }

    $eventId = $_GET['event_id'];
    $userId = $_GET['user_id'];

    try {
        // Check if the user is signed up for the event
        $stmt = $pdo->prepare("SELECT * FROM event_signups WHERE event_id = :event_id AND user_id = :user_id");
        $stmt->execute([':event_id' => $eventId, ':user_id' => $userId]);
        $signedUp = $stmt->fetch();

        echo json_encode(["signed_up" => (bool) $signedUp]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to check user signup status"]);
    }
}

function getUserProfile($pdo) {
    $sessionToken = $_SESSION['session_token'] ?? $_GET['session_token'] ?? null;

    if (!$sessionToken) {
        echo json_encode(["error" => "Session token is required"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT u.firstname, u.lastname, u.username, u.phone 
            FROM users u
            JOIN session_tokens s ON u.id_user = s.id_user
            WHERE s.token = :session_token
        ");
        $stmt->execute([':session_token' => $sessionToken]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(["error" => "User not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to fetch user profile"]);
    }
}

/**
 * Fetch all invites for a given event.
 */
function getInvites($pdo) {
    if (!isset($_SESSION['username'])) {
        echo json_encode(["error" => "User not authenticated"]);
        return;
    }

    if (!isset($_GET['event_id'])) {
        echo json_encode(["error" => "Event ID is required"]);
        return;
    }

    $eventId = $_GET['event_id'];
    $invitedBy = $_SESSION['username'];

    try {
        $stmt = $pdo->prepare("
            SELECT ei.id_event_invite, u.username, ei.status, ei.invited_by
            FROM event_invites ei
            JOIN users u ON ei.id_user = u.id_user
            WHERE ei.id_event = :event_id
            AND ei.invited_by = :invited_by
        ");
        $stmt->execute([
            ':event_id' => $eventId,
            ':invited_by' => $invitedBy
        ]);
        $invites = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($invites);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to fetch invites"]);
    }
}

/**
 * Update the status of an invitation.
 */
function updateInvite($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id_event_invite']) || !isset($data['status'])) {
        echo json_encode(["error" => "Invite ID and status are required"]);
        return;
    }

    $inviteId = $data['id_event_invite'];
    $status = $data['status'];

    try {
        $stmt = $pdo->prepare("
            UPDATE event_invites 
            SET status = :status 
            WHERE id_event_invite = :invite_id
        ");
        $stmt->execute([':status' => $status, ':invite_id' => $inviteId]);

        echo json_encode(["success" => "Invite status updated"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to update invite"]);
    }
}

/**
 * Delete an invitation.
 */
function deleteInvite($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id_event_invite'])) {
        echo json_encode(["error" => "Invite ID is required"]);
        return;
    }

    $inviteId = $data['id_event_invite'];

    try {
        $stmt = $pdo->prepare("DELETE FROM event_invites WHERE id_event_invite = :invite_id");
        $stmt->execute([':invite_id' => $inviteId]);

        echo json_encode(["success" => "Invite deleted"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to delete invite"]);
    }
}