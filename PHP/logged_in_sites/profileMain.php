<?php
include_once 'logged_header.php';
require_once '../functions.php';
require_once '../config.php';

tokenVerify($dbHost, $dbName, $dbUser, $dbPass);
// Fetch user details
try
{
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    die("Database connection failed: " . $e->getMessage());
}
$userId = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT firstname, lastname, username, phone FROM users WHERE username = :user");
$stmt->bindValue(':user', $userId, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user)
{
    die("No user found with the provided username.");
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <title>Profile page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #BBB8B2;
            color: #2E2E3A;
        }
        header, footer {
            background-color: #2E2E3A;
            color: #FFF;
        }
        .btn-custom {
            background-color: #DE9151;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #BC5D2E;
        }
        input.form-control {
            border: 1px solid #BC5D2E;
        }
        input.form-control:focus {
            border-color: #DE9151;
            box-shadow: 0 0 5px #DE9151;
        }
        h1, h2 {
            color: #F34213;
        }
        .card {
            background-color: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<header class="p-3 text-center">
    <h1>Welcome to Your Profile Page!</h1>
</header>

<div class="container mt-5">
    <!-- User Details Form -->
    <div class="card p-4 mb-4">
        <h2 class="mb-3">Your Details</h2>
        <form id="profileForm" class="row g-3">
            <div class="col-md-6">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="form-control">
            </div>
            <div class="col-12 text-end">
                <button type="button" id="saveProfile" class="btn btn-custom">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Display subscribed events -->
    <div class="card p-4 mb-2">
        <h2 class="mb-3">My subscribed Events</h2>
        <div id="eventList">Loading events...</div>
    </div>

    <!-- Display my events -->
    <div class="card p-4">
        <h2 class="mb-3">My Events</h2>
        <div id="eventList2">Loading events...</div>
    </div>
</div>

<!-- Footer -->
<footer class="p-3 text-center mt-5">
    <p>&copy; 2024 My Profile Page</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/readMore.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Fetch user's events when the page loads
        fetchEvents();
        fetchEvents2();

        // Handle the Save Profile button click
        document.getElementById('saveProfile').addEventListener('click', async () => {
            // Collect form data
            const form = document.getElementById('profileForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('updateProfile.php', {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();
                alert(text); // Display the server response
            } catch (error) {
                console.error('Error saving profile:', error);
                alert('An error occurred while updating the profile!');
            }
        });

        // Fetch events function
        async function fetchEvents() {
            try {
                const response = await fetch('fetchEvents.php');
                if (!response.ok) throw new Error('Network response was not ok');
                const html = await response.text();
                document.getElementById('eventList').innerHTML = html;
            } catch (error) {
                console.error('Error fetching events:', error);
                document.getElementById('eventList').innerHTML = '<p>You don\'t have any events here.</p>';
            }
        }

        async function fetchEvents2() {
            try {
                const response = await fetch('fetchEvents2.php');
                if (!response.ok) throw new Error('Network response was not ok');
                document.getElementById('eventList2').innerHTML = await response.text();
            } catch (error) {
                console.error('Error fetching events:', error);
                document.getElementById('eventList2').innerHTML = '<p>You don\'t have any events here.</p>';
            }
        }
    });
</script>

<?php
include_once 'logged_footer.php';
?>
</body>
</html>
