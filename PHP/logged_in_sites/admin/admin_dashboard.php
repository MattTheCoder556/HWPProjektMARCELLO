<?php
session_start();

// Check if the user is an admin
/*if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // If not admin, redirect to login page
    header('Location: login.php');
    exit;
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .dashboard-button {
            width: 200px;
            height: 60px;
            font-size: 18px;
            margin: 10px;
        }
        .container {
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Admin Dashboard!</h1>

        <div class="mt-5">
            <a href="admin_users.php" class="btn btn-primary dashboard-button">Manage Users</a>
            <a href="admin_events.php" class="btn btn-success dashboard-button">Manage Events</a>
        </div>

        <div class="mt-4">
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>
