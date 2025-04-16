<?php
require_once '../config.php';
require_once '../functions.php';

// Hibakeresés bekapcsolása
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Naplózzuk a bejövő paramétereket
error_log("[Gift Claim] Kezdeményezés érkezett. Token: " . ($_GET['token'] ?? 'NINCS') . " | Ajándék: " . ($_GET['gift'] ?? 'NINCS'));

try {
    // Adatbázis kapcsolat
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Kötelező paraméterek ellenőrzése
    if (empty($_GET['token']) || empty($_GET['gift'])) {
        throw new Exception("Hiányzó token vagy ajándék paraméter");
    }

    $token = $_GET['token'];
    $gift = urldecode($_GET['gift']);

    // 1. Token létezésének és érvényességének ellenőrzése
    $stmt = $pdo->prepare("SELECT * FROM event_invites WHERE invite_token = ? AND invite_expire > NOW()");
    $stmt->execute([$token]);
    $invite = $stmt->fetch();

    if (!$invite) {
        error_log("[Gift Claim] Érvénytelen vagy lejárt token: $token");
        header("Location: ../index.php?error=invalidtoken");
        exit();
    }

    // 2. Kívánságlista ellenőrzése
    $stmt = $pdo->prepare("SELECT wishes FROM gift_wishlists WHERE id_event = ?");
    $stmt->execute([$invite['id_event']]);
    $wishlist = $stmt->fetch();

    if (!$wishlist) {
        error_log("[Gift Claim] Nincs kívánságlista az eseményhez: {$invite['id_event']}");
        header("Location: ../index.php?error=nowishlist");
        exit();
    }

    $wishlistItems = json_decode($wishlist['wishes'], true) ?: [];

    if (!in_array($gift, $wishlistItems, true)) {
        error_log("[Gift Claim] Érvénytelen ajándék: '$gift'. Elérhető ajándékok: " . implode(', ', $wishlistItems));
        header("Location: ../index.php?error=invalidgift");
        exit();
    }

    // 3. Adatbázis frissítése tranzakcióban
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

        // b) Ajándék eltávolítása a kívánságlistáról
        $updatedWishes = array_values(array_diff($wishlistItems, [$gift]));
        $stmt = $pdo->prepare("UPDATE gift_wishlists SET wishes = ? WHERE id_event = ?");
        $stmt->execute([json_encode($updatedWishes), $invite['id_event']]);

        $pdo->commit();
        error_log("[Gift Claim] Sikeres Foglalás: $gift");
        header("Location: ../invitation_confirmation.php?claim=success&gift=" . urlencode($gift));
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("[Gift Claim] Adatbázis hiba: " . $e->getMessage());
        header("Location: ../index.php?error=already_claimed");
        exit();
    }

} catch (Exception $e) {
    error_log("[Gift Claim] Rendszerhiba: " . $e->getMessage());
    header("Location: ../index.php?error=system");
    exit();
}