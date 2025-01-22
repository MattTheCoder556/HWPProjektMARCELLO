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
</div>

<!-- Invite People Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F34213; color: white;">
                <h5 class="modal-title" id="inviteModalLabel">Invite People to Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">
                <form id="inviteForm">
                    <input type="hidden" id="eventId" name="event_id">
                    <div class="mb-3">
                        <label for="inviteEmail" class="form-label">Invitee's Email</label>
                        <input type="email" class="form-control" id="inviteEmail" name="email" placeholder="Enter email address" required>
                    </div>
                    <div id="inviteMessage" class="text-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="sendInviteButton" class="btn btn-primary" style="background-color: #DE9151;">Send Invite</button>
            </div>
        </div>
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

        const inviteModalElement = document.getElementById("inviteModal");
        const inviteModal = new bootstrap.Modal(inviteModalElement);

        window.openInviteModal = function (eventId) {
            document.getElementById("eventId").value = eventId; // Set the event ID in the hidden field
            document.getElementById("inviteMessage").style.display = "none";
            document.getElementById("inviteEmail").value = "";
            inviteModal.show();
        };

        // Close modal when the close button is clicked
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(closeButton => {
            closeButton.addEventListener('click', () => {
                inviteModal.hide(); // Hide the modal programmatically
                // console.log('Modal closed via close button'); // Debug log
            });
        });

        // Send the invite via AJAX
        document.getElementById("sendInviteButton").addEventListener("click", async () => {
            const eventId = document.getElementById("eventId").value;
            const email = document.getElementById("inviteEmail").value;

            if (!email) {
                displayInviteMessage("Email is required.");
                return;
            }

            try {
                const response = await fetch("inviteHandler.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ event_id: eventId, email: email }),
                });

                const text = await response.text(); // Get raw text response

                /*

                FONTOS: Ha ilyesmi hiba van:

                Unexpected token '<', "<br />
                <fo"... is not valid JSON

                A megoldás, hogy kiíratod a nyers választ a szerverről!!! (felül)
                 */
                // console.log("Raw response:", text);

                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
//                    console.error("Failed to parse JSON:", jsonError);
                    displayInviteMessage("Invalid server response. Check logs for details.");
                    return;
                }

                if (result.error) {
//                    console.error("Error:", result.error);
                    displayInviteMessage(result.error);
                } else {
                    alert("Invite sent successfully!");
                    inviteModal.hide();
                    document.getElementById("inviteMessage").style.display = "none";
                }
            }
            catch (error) {
//                console.error("Fetch error:", error);
                displayInviteMessage("Failed to send the invite. Please try again later.");
            }
        });

        // Display error message in the modal
        function displayInviteMessage(message) {
            const messageBox = document.getElementById("inviteMessage");
            messageBox.textContent = message;
            messageBox.style.display = "block";
        }

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
        const url = 'http://localhost/HWPProjektMARCELLO/PHP/api.php?action=getUserProfile&session_token=' + encodeURIComponent('<?php echo $sessionToken; ?>');
        console.log('Requesting: ' + url);
        
        const response = await fetch(url);

        console.log('Raw Response: ', response);

        if (!response.ok) {
            throw new Error('HTTP error: ' + response.status);
        }

        const textResponse = await response.text();
        console.log('Raw JSON:', textResponse);


        const userProfile = JSON.parse(textResponse);
        console.log('User Profile Response:', userProfile); // Log the entire response

        if (userProfile.error) {
            alert(userProfile.error);
            return;
        }

        if (userProfile.firstname && userProfile.lastname && userProfile.username && userProfile.phone) {
            // Fill the form with user profile data
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
