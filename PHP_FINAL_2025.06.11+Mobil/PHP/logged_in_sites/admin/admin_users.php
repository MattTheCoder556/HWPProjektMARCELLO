<?php
session_start();

// Check if the user is logged in and is an admin
/*if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // Redirect to login if not an admin
    header('Location: login.php');
    exit;
}*/

require_once "../../config.php";

// Fetch all users from the database
$sql = "SELECT id_user, firstname, lastname, username, phone, is_verified, is_banned FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table {
            margin-top: 20px;
        }
        .action-btn {
            width: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Manage Users</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id_user']); ?></td>
                    <td><?php echo htmlspecialchars($user['firstname']) . " " . htmlspecialchars($user['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td>
                        <?php echo $user['is_verified'] ? "Verified" : "Not Verified"; ?><br>
                        <?php echo $user['is_banned'] ? "Banned" : "Active"; ?>
                    </td>
                    <td>
                        <!-- Edit Button -->
<a href="edit_users.php?id_user=<?= $user['id_user'] ?>" class="btn btn-warning btn-md">Edit</a>
                        
                        <!-- Ban/Unban User -->
                        <?php if ($user['is_banned']): ?>
                            <a href="toggle_ban.php?id=<?php echo $user['id_user']; ?>&action=unban" class="btn btn-success action-btn">Unban</a>
                        <?php else: ?>
                            <a href="toggle_ban.php?id=<?php echo $user['id_user']; ?>&action=ban" class="btn btn-danger action-btn">Ban</a>
                        <?php endif; ?>

                        <!-- Delete Button -->
                        <form method="POST" action="delete_user.php" style="display: inline;">
                            <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                            <button type="submit" class="btn btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
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
