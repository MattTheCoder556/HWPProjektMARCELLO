<?php
include_once 'logged_header.php';
require_once '../functions.php';
require_once '../config.php';

// Assuming session token is stored in the session
$sessionToken = $_SESSION['session_token'];
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <title>Profile page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
                <input type="text" id="firstname" name="firstname" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" id="lastname" name="lastname" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control">
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
    <div class="text-center my-4">
    <a href="wishlist_events.php" class="btn btn-secondary">View My Wishlisted Events</a>
</div>
</div>

<!-- Footer -->
<footer class="p-3 text-center mt-5">
    <p>&copy; 2024 My Profile Page</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Fetch user profile data
        fetchUserProfile();

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

        // Fetch user profile data function
        async function fetchUserProfile() {
            try {
                const url = 'http://localhost/HWP_2024/MammaMiaMarcello/PHP/api.php?action=getUserProfile&session_token=' + encodeURIComponent('<?php echo $sessionToken; ?>');
                const response = await fetch(url);
                if (!response.ok) throw new Error('HTTP error: ' + response.status);

                const textResponse = await response.text();
                const userProfile = JSON.parse(textResponse);

                if (userProfile.error) {
                    alert(userProfile.error);
                    return;
                }

                if (userProfile.firstname && userProfile.lastname && userProfile.username && userProfile.phone) {
                    document.getElementById('firstname').value = userProfile.firstname;
                    document.getElementById('lastname').value = userProfile.lastname;
                    document.getElementById('username').value = userProfile.username;
                    document.getElementById('phone').value = userProfile.phone;
                } else {
                    alert('Error: Missing profile data.');
                }
            } catch (error) {
                console.error('Error fetching user profile:', error);
                alert('Error fetching user profile.');
            }
        }

        // Fetch events function
        async function fetchEvents() {
            try {
                const response = await fetch('fetchEvents.php');
                if (!response.ok) throw new Error('Network response was not ok');
                const html = await response.text();
                document.getElementById('eventList').innerHTML = html;
            } catch (error) {
                document.getElementById('eventList').innerHTML = '<p>You don\'t have any events here.</p>';
            }
        }

        async function fetchEvents2() {
            try {
                const response = await fetch('fetchEvents2.php');
                if (!response.ok) throw new Error('Network response was not ok');
                document.getElementById('eventList2').innerHTML = await response.text();
            } catch (error) {
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
