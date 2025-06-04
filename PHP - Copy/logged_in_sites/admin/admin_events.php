<?php
session_start();

// Check if the user is logged in and is an admin
/*if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}*/

require_once "../../config.php";

// Fetch all events from the database
$sql = "SELECT * FROM events";
$stmt = $pdo->query($sql);
$events = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table {
            margin-top: 20px;
        }
        .action-btn {
            width: 100px;
        }
        .event-pic {
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Manage Events</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Owner</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Event Pic</th>
                    <th>Attendees</th>
                    <th>Is Banned</th>
                    <th>Event Type</th>
                    <th>Description</th>
                    <th>Place</th>
                    <th>Guest List</th>
                    <th>Public</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                    <td><?php echo htmlspecialchars($event['owner']); ?></td>
                    <td><?php echo htmlspecialchars($event['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($event['end_date']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($event['event_pic']); ?>" alt="Event Picture" class="event-pic"></td>
                    <td><?php echo htmlspecialchars($event['attendees']); ?></td>
                    <td><?php echo $event['is_banned'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo htmlspecialchars($event['event_type']); ?></td>
                    <td><?php echo htmlspecialchars($event['description']); ?></td>
                    <td><?php echo htmlspecialchars($event['place']); ?></td>
                    <td><?php echo htmlspecialchars($event['guest_list']); ?></td>
                    <td><?php echo $event['public'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <!-- Edit Button -->
                        <a href="edit_event.php?id_event=<?php echo $event['id_event']; ?>" class="btn btn-warning action-btn">Edit</a>

                        <!-- Delete Button -->
                        <form method="POST" action="delete_event.php" style="display: inline;">
                            <input type="hidden" name="id_event" value="<?php echo $event['id_event']; ?>">
                            <button type="submit" class="btn btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this event?');">Delete</button>
                        </form>

                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</body>
</html>
