<?php
include_once "../config.php";
include_once "../functions.php";
tokenVerify($dbHost, $dbName, $dbUser, $dbPass);

header('Content-Type: application/json');

function createUrlFriendlyVersion($item) {
    return preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower(trim($item)));
}

try {
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $action = $_GET['action'] ?? null;

    if ($action === "getItems") {
        $eventId = $_GET['event_id'] ?? null;
        if (!$eventId) throw new Exception("Event ID is required.");

        $stmt = $pdo->prepare("SELECT wishes FROM gift_wishlists WHERE id_event = :event_id");
        $stmt->execute([':event_id' => $eventId]);
        $wishlist = $stmt->fetchColumn();

        echo json_encode([
            "wishes" => $wishlist ? json_decode($wishlist, true) : []
        ]);
    } 
    elseif ($action === "addItem") {
        $data = json_decode(file_get_contents("php://input"), true);
        $eventId = $data['event_id'] ?? null;
        $item = trim($data['item'] ?? '');

        if (!$eventId || !$item) throw new Exception("Event ID and item are required.");

        // Get existing data - FIXED HERE
        $stmt = $pdo->prepare("SELECT wishes, url_friendly_wishes FROM gift_wishlists WHERE id_event = :event_id");
        $stmt->execute([':event_id' => $eventId]);
        $current = $stmt->fetch();

        // Initialize arrays safely
        $items = [];
        $urlItems = [];
        
        if ($current) {
            $items = $current['wishes'] ? json_decode($current['wishes'], true) : [];
            $urlItems = $current['url_friendly_wishes'] ? json_decode($current['url_friendly_wishes'], true) : [];
        }

        $items[] = $item;
        $urlItems[] = createUrlFriendlyVersion($item);

        $stmt = $pdo->prepare("INSERT INTO gift_wishlists (id_event, wishes, url_friendly_wishes) 
                               VALUES (:event_id, :wishes, :url_wishes) 
                               ON DUPLICATE KEY UPDATE wishes = :wishes, url_friendly_wishes = :url_wishes");
        $stmt->execute([
            ':event_id' => $eventId,
            ':wishes' => json_encode($items),
            ':url_wishes' => json_encode($urlItems)
        ]);

        echo json_encode(["success" => true]);
    } 
    elseif ($action === "removeItem") {
        $data = json_decode(file_get_contents("php://input"), true);
        $eventId = $data['event_id'] ?? null;
        $item = trim($data['item'] ?? '');

        if (!$eventId || !$item) throw new Exception("Event ID and item are required.");

        // Get current data - FIXED HERE TOO
        $stmt = $pdo->prepare("SELECT wishes, url_friendly_wishes FROM gift_wishlists WHERE id_event = :event_id");
        $stmt->execute([':event_id' => $eventId]);
        $current = $stmt->fetch();

        $items = [];
        $urlItems = [];
        
        if ($current) {
            $items = $current['wishes'] ? json_decode($current['wishes'], true) : [];
            $urlItems = $current['url_friendly_wishes'] ? json_decode($current['url_friendly_wishes'], true) : [];
        }

        $index = array_search($item, $items);
        if ($index !== false) {
            unset($items[$index]);
            unset($urlItems[$index]);
            $items = array_values($items);
            $urlItems = array_values($urlItems);

            $stmt = $pdo->prepare("UPDATE gift_wishlists SET wishes = :wishes, url_friendly_wishes = :url_wishes WHERE id_event = :event_id");
            $stmt->execute([
                ':wishes' => json_encode($items),
                ':url_wishes' => json_encode($urlItems),
                ':event_id' => $eventId
            ]);
        }

        echo json_encode(["success" => true]);
    } 
    else {
        throw new Exception("Invalid action.");
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}