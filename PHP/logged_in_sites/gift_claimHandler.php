<?php
require_once '../config.php';
require_once '../functions.php';

session_start();

// Segédfüggvény ajándéknév normalizáláshoz
function normalizeGiftName($name) {
    return preg_replace('/[^a-z0-9]/', '', strtolower($name));
}

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    if (empty($_GET['token']) || empty($_GET['gift'])) {
        throw new Exception("Hiányzó token vagy ajándék paraméter");
    }

    $token = $_GET['token'];
    $gift = urldecode($_GET['gift']);

    // 1. Token ellenőrzése
    $stmt = $pdo->prepare("SELECT *, TIMESTAMPDIFF(MINUTE, NOW(), invite_expire) as expires_in 
                          FROM event_invites 
                          WHERE invite_token = ?");
    $stmt->execute([$token]);
    $invite = $stmt->fetch();

    if (!$invite) {
        $_SESSION['flash_error'] = "Érvénytelen meghívó link";
        header("Location: ../index.php");
        exit();
    }

    if ($invite['invite_expire'] < date('Y-m-d H:i:s')) {
        $_SESSION['flash_error'] = "A meghívó link lejárt";
        header("Location: ../index.php");
        exit();
    }

    // 2. Kívánságlista ellenőrzése
    $stmt = $pdo->prepare("SELECT wishes FROM gift_wishlists WHERE id_event = ?");
    $stmt->execute([$invite['id_event']]);
    $wishlist = $stmt->fetch();

    if (!$wishlist) {
        $_SESSION['flash_error'] = "Nincs kívánságlista ehhez az eseményhez";
        header("Location: ../index.php");
        exit();
    }

    $wishlistItems = json_decode($wishlist['wishes'], true) ?: [];

    // Normalizált összehasonlítás
    $normalizedWishlist = array_map('normalizeGiftName', $wishlistItems);
    $normalizedGift = normalizeGiftName($gift);
    $foundIndex = array_search($normalizedGift, $normalizedWishlist);

    if ($foundIndex === false) {
        $_SESSION['flash_error'] = "Az ajándék nem található a kívánságlistán";
        header("Location: ../index.php");
        exit();
    }

    // Az eredeti, nem módosított ajándéknévvel dolgozunk tovább
    $gift = $wishlistItems[$foundIndex];

    // 3. Adatbázis frissítése
    $pdo->beginTransaction();

    try {
        // a) Kiválasztott ajándék mentése
        $stmt = $pdo->prepare("UPDATE event_invites 
                            SET selected_gift = ?, gift_selected = TRUE 
                            WHERE id_event_invite = ? AND gift_selected = FALSE");
        $stmt->execute([$gift, $invite['id_event_invite']]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Már történt ajándékválasztás ehhez a meghívóhoz");
        }

        // b) Ajándék eltávolítása a listáról
        $updatedWishes = array_values(array_diff($wishlistItems, [$gift]));
        $stmt = $pdo->prepare("UPDATE gift_wishlists SET wishes = ? WHERE id_event = ?");
        $stmt->execute([json_encode($updatedWishes), $invite['id_event']]);

        $pdo->commit();

        $_SESSION['flash_success'] = "Sikeres ajándékfoglalás: " . htmlspecialchars($gift);
        header("Location: ../index.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = $e->getMessage();
        header("Location: ../index.php");
        exit();
    }

} catch (Exception $e) {
    $_SESSION['flash_error'] = "Váratlan hiba történt: " . $e->getMessage();
    header("Location: ../index.php");
    exit();
}