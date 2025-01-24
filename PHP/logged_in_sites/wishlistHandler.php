<?php
include_once "../config.php";
include_once "../functions.php";
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $action = $_GET['action'] ?? null;

    if ($action === "getItems") {
        // Fetch wishlist items for an event
        $eventId = $_GET['event_id'] ?? null;

        if (!$eventId) {
            throw new Exception("Event ID is required.");
        }

        $stmt = $pdo->prepare("SELECT wishes FROM gift_wishlists WHERE id_event = :event_id");
        $stmt->execute([':event_id' => $eventId]);
        $wishlist = $stmt->fetchColumn();

        echo json_encode([
            "wishes" => $wishlist ? json_decode($wishlist, true) : []
        ]);
    } elseif ($action === "addItem") {
        // Add a wishlist item
        $data = json_decode(file_get_contents("php://input"), true);
        $eventId = $data['event_id'] ?? null;
        $item = $data['item'] ?? null;

        if (!$eventId || !$item) {
            throw new Exception("Event ID and item are required.");
        }

        $stmt = $pdo->prepare("SELECT wishes FROM gift_wishlists WHERE id_event = :event_id");
        $stmt->execute([':event_id' => $eventId]);
        $wishlist = $stmt->fetchColumn();

        $items = $wishlist ? json_decode($wishlist, true) : [];
        $items[] = $item;

        $stmt = $pdo->prepare("INSERT INTO gift_wishlists (id_event, wishes, chosen_gifts) 
                               VALUES (:event_id, :wishes, '') 
                               ON DUPLICATE KEY UPDATE wishes = :wishes");
        $stmt->execute([
            ':event_id' => $eventId,
            ':wishes' => json_encode($items),
        ]);

        echo json_encode(["success" => true]);
    } elseif ($action === "removeItem") {
        // Remove a wishlist item
        $data = json_decode(file_get_contents("php://input"), true);
        $eventId = $data['event_id'] ?? null;
        $item = $data['item'] ?? null;

        if (!$eventId || !$item) {
            throw new Exception("Event ID and item are required.");
        }

        $stmt = $pdo->prepare("SELECT wishes FROM gift_wishlists WHERE id_event = :event_id");
        $stmt->execute([':event_id' => $eventId]);
        $wishlist = $stmt->fetchColumn();

        $items = $wishlist ? json_decode($wishlist, true) : [];
        // Remove the item and reindex the array
        $items = array_values(array_filter($items, fn($i) => $i !== $item));

        $stmt = $pdo->prepare("UPDATE gift_wishlists SET wishes = :wishes WHERE id_event = :event_id");
        $stmt->execute([
            ':wishes' => json_encode($items),
            ':event_id' => $eventId
        ]);

        echo json_encode(["success" => true]);
    }
    else {
        throw new Exception("Invalid action.");
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
