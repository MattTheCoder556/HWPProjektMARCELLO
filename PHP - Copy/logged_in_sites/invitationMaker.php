<?php
include_once "../config.php";
session_start();

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Fetch event data using the event ID
    $query = "SELECT * FROM events WHERE id_event = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        echo "Event not found.";
        exit();
    }
} else {
    echo "No event ID provided.";
    exit();
}

// Check if a style is selected from the form
if (isset($_GET['style'])) {
    $_SESSION['style'] = $_GET['style'];
} elseif (!isset($_SESSION['style'])) {
    $_SESSION['style'] = 'style1'; // Default style
}

$style = $_SESSION['style'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Invitation</title>
    <!-- Link to the stylesheets -->
    <link rel="stylesheet" href="../../assets/css/styles.css"> <!-- Your general CSS file -->
    <?php if ($style == 'style1'): ?>
        <link rel="stylesheet" href="styles/style1.css">
    <?php elseif ($style == 'style2'): ?>
        <link rel="stylesheet" href="styles/style2.css">
    <?php elseif ($style == 'style3'): ?>
        <link rel="stylesheet" href="styles/style3.css">
    <?php endif; ?>
</head>
<body>
<?php include_once "logged_header.php"; ?>
<form action="invitationMaker.php" method="GET">
    <label for="style">Choose an Invitation Style:</label>
    <select name="style" id="style">
        <option value="style1" <?php echo $style == 'style1' ? 'selected' : ''; ?>>Style 1</option>
        <option value="style2" <?php echo $style == 'style2' ? 'selected' : ''; ?>>Style 2</option>
        <option value="style3" <?php echo $style == 'style3' ? 'selected' : ''; ?>>Style 3</option>
    </select>
    <button type="submit">Select Style</button>
</form>

<div class="invitation-container">
    <h1 class="invitation-title"><?= htmlspecialchars($event['event_name']) ?></h1>
    
    <div class="event-details">
        <p><strong>Event Date:</strong> <?= htmlspecialchars($event['start_date']) ?> to <?= htmlspecialchars($event['end_date']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($event['place']) ?></p>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
    </div>

    <?php if ($event['event_pic']): ?>
        <img src="<?= htmlspecialchars($event['event_pic']) ?>" alt="Event Image" class="event-image">
    <?php endif; ?>

    <div class="invitation-message">
        <h2>You're Invited!</h2>
        <p>We would love for you to join us at <?= htmlspecialchars($event['event_name']) ?>. It's going to be a memorable event!</p>
        <button onclick="window.print()">Print Invitation</button>
    </div>
</div>

<?php include_once "logged_footer.php"; ?>
</body>
</html>
