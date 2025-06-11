<?php
session_start();

// Check if the user is logged in and is an admin
/*if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}*/

require_once "../../config.php";

if (isset($_GET['id_event'])) {
    $id_event = (int)$_GET['id_event'];

    // Fetch the event data
    $sql = "SELECT * FROM events WHERE id_event = :id_event";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_event', $id_event, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die("Event not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update event in the database
    $event_name = $_POST['event_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = $_POST['description'];
    $place = $_POST['place'];
    $public = isset($_POST['public']) ? 1 : 0;

    $sql = "UPDATE events SET event_name = :event_name, start_date = :start_date, end_date = :end_date, description = :description, place = :place, public = :public WHERE id_event = :id_event";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':event_name', $event_name);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':place', $place);
    $stmt->bindParam(':public', $public);
    $stmt->bindParam(':id_event', $id_event);
    $stmt->execute();

    // Redirect back to events page after update
    header('Location: admin_events.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>

    <!-- Add your admin CSS here, or link an external stylesheet -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input, textarea {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Event</h1>
    <form method="POST" action="edit_event.php?id_event=<?php echo $event['id_event']; ?>">
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" id="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required><br>

        <label for="start_date">Start Date:</label>
        <input type="datetime-local" name="start_date" id="start_date" value="<?php echo date('Y-m-d\TH:i', strtotime($event['start_date'])); ?>" required><br>

        <label for="end_date">End Date:</label>
        <input type="datetime-local" name="end_date" id="end_date" value="<?php echo date('Y-m-d\TH:i', strtotime($event['end_date'])); ?>" required><br>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required><?php echo htmlspecialchars($event['description']); ?></textarea><br>

        <label for="place">Place:</label>
        <input type="text" name="place" id="place" value="<?php echo htmlspecialchars($event['place']); ?>" required><br>

        <label for="public">Public:</label>
        <input type="checkbox" name="public" id="public" <?php echo $event['public'] == 1 ? 'checked' : ''; ?>><br>

        <input type="submit" value="Update Event">
    </form>
</div>

</body>
</html>
