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
    <div class="container my-5">
    <a href="event_history.php" class="btn btn-info">View Event History</a>
</div>

</div>
</div>

<!-- Invite People Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #F34213; color: white;">
                <h5 class="modal-title" id="inviteModalLabel">Customize and Send Invitation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Template Configurator -->
                <form id="inviteForm">
                    <input type="hidden" id="eventId" name="event_id">

                    <!-- Template Selection -->
                    <div class="mb-3">
                        <label for="templateSelect" class="form-label">Choose Template</label>
                        <select id="templateSelect" class="form-select">
                            <option value="default">Default Template</option>
                            <option value="formal">Formal Template</option>
                            <option value="custom">Custom Invitation</option>
                        </select>
                    </div>

                    <!-- Editable Fields -->
                    <div id="customFields">
                        <div class="mb-3">
                            <label for="eventName" class="form-label">Event Name</label>
                            <input type="text" id="eventName" class="form-control" placeholder="Enter event name">
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">Event Description</label>
                            <textarea id="eventDescription" class="form-control" placeholder="Enter event description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="backgroundColor" class="form-label">Background Color</label>
                            <input type="color" id="backgroundColor" class="form-control form-control-color" value="#ffffff">
                        </div>
                        <div class="mb-3">
                            <label for="fontColor" class="form-label">Font Color</label>
                            <input type="color" id="fontColor" class="form-control form-control-color" value="#000000">
                        </div>
                        <div class="mb-3">
                            <label for="customImage" class="form-label">Upload Image</label>
                            <input type="file" id="customImage" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="externalLink" class="form-label">Add External Link</label>
                            <input type="url" id="externalLink" class="form-control" placeholder="Enter link">
                        </div>
                    </div>

                    <!-- Include Wishlist -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="includeWishlistCheckbox">
                        <label class="form-check-label" for="includeWishlistCheckbox">
                            Include Wishlist in Invitation
                        </label>
                    </div>

                    <!-- Live Preview -->
                    <div class="mb-3">
                        <h6>Live Preview</h6>
                        <div id="livePreview" style="border: 1px solid #ccc; padding: 10px; background-color: #ffffff;">
                            <h4 id="previewEventName">Event Name</h4>
                            <p id="previewEventDescription">Event Description</p>
                            <a id="previewExternalLink" href="#" target="_blank" style="color: blue; text-decoration: underline;">External Link</a>
                        </div>
                    </div>

                    <!-- Invitee Email -->
                    <div class="mb-3">
                        <label for="inviteEmail" class="form-label">Invitee's Email</label>
                        <input type="email" class="form-control" id="inviteEmail" name="email" placeholder="Enter email address" required>
                    </div>
                    <div id="inviteMessage" class="text-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="sendInviteButton" class="btn btn-primary" style="background-color: #DE9151;">
                    Send Invite
                    <span id="loadingSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Wishlist Modal -->
<div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #28a745; color: white;">
                <h5 class="modal-title" id="wishlistModalLabel">Manage Wishlist for Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="wishlistItems">
                    <!-- Existing wishlist items will be rendered here -->
                </div>
                <form id="wishlistForm">
                    <input type="hidden" id="wishlistEventId" name="event_id">
                    <div class="mb-3">
                        <label for="wishlistItem" class="form-label">Add Item</label>
                        <input type="text" class="form-control" id="wishlistItem" name="item" placeholder="Enter item name" required>
                    </div>
                    <button type="button" id="addWishlistItemButton" class="btn btn-success">Add to Wishlist</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="p-3 text-center mt-5">
    <p>&copy; 2025 My Profile Page</p>
</footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // Fetch user profile data and events
        fetchUserProfile();
        fetchEvents();
        fetchEvents2();

        // Constants

        const inviteModalElement = document.getElementById("inviteModal");
        const inviteModal = new bootstrap.Modal(inviteModalElement);
        const wishlistModalElement = document.getElementById("wishlistModal");
        const wishlistModal = new bootstrap.Modal(wishlistModalElement);
        const eventNameInput = document.getElementById("eventName");
        const eventDescriptionInput = document.getElementById("eventDescription");
        const customImageInput = document.getElementById("customImage");
        const backgroundColorInput = document.getElementById("backgroundColor");
        const externalLinkInput = document.getElementById("externalLink");
        const fontColorInput = document.getElementById("fontColor");

        // Live preview elements
        const livePreview = document.getElementById("livePreview");
        const previewEventName = document.getElementById("previewEventName");
        const previewEventDescription = document.getElementById("previewEventDescription");
        const previewExternalLink = document.getElementById("previewExternalLink");
        let uploadedImagePreview = null;


                //  Invite modal //



        window.openInviteModal = function (eventId) {
            document.getElementById("eventId").value = eventId; // Set the event ID in the hidden field
            document.getElementById("inviteMessage").style.display = "none";
            document.getElementById("inviteEmail").value = "";
            document.getElementById("includeWishlistCheckbox").checked = false;

            // Reset preview to default
            previewEventName.textContent = "Event Name";
            previewEventDescription.textContent = "Event Description";
            livePreview.style.backgroundColor = "#ffffff";
            previewExternalLink.href = "#";
            previewExternalLink.textContent = "External Link";
            previewExternalLink.style.display = "none";

            // Reset the uploaded image input and remove the preview
            customImageInput.value = "";
            let uploadedImagePreview;
            if (uploadedImagePreview)
            {
                uploadedImagePreview.remove(); // Remove the preview element
                uploadedImagePreview = null;
            }

            inviteModal.show();
        };

        // Reset inputs when modal is hidden
        inviteModalElement.addEventListener("hidden.bs.modal", () => {
            // Reset all input fields
            eventNameInput.value = "";
            eventDescriptionInput.value = "";
            backgroundColorInput.value = "#ffffff";
            fontColorInput.value = "#000000";
            externalLinkInput.value = "";

            // Reset live preview to default values
            previewEventName.textContent = "Event Name";
            previewEventDescription.textContent = "Event Description";
            livePreview.style.backgroundColor = "#ffffff";
            previewEventName.style.color = "#000000";
            previewEventDescription.style.color = "#000000";
            previewExternalLink.style.color = "#000000";
            previewExternalLink.href = "#";
            previewExternalLink.textContent = "External Link";
            previewExternalLink.style.display = "none";

            // Reset the uploaded image input and remove the preview
            customImageInput.value = "";
            if (uploadedImagePreview) {
                uploadedImagePreview.remove(); // Remove the preview element
                uploadedImagePreview = null;
            }
        });

        // Update live preview when font color changes
        fontColorInput.addEventListener("input", () => {
            const selectedColor = fontColorInput.value;
            previewEventName.style.color = selectedColor;
            previewEventDescription.style.color = selectedColor;
            previewExternalLink.style.color = selectedColor;
        });

        // Update the external link in the preview
        externalLinkInput.addEventListener("input", () => {
            if (externalLinkInput.value) {
                previewExternalLink.href = externalLinkInput.value;
                previewExternalLink.textContent = "Visit Link";
                previewExternalLink.style.display = "inline";
            } else {
                previewExternalLink.style.display = "none";
            }
        });

        // Listen for image uploads to display a preview
        customImageInput.addEventListener("change", (event) => {
            // Remove the old preview if it exists
            if (uploadedImagePreview) {
                uploadedImagePreview.remove();
                uploadedImagePreview = null;
            }

            const file = event.target.files[0];
            if (file) {
                // Ensure the file is an image
                if (file.type.startsWith("image/")) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        // Create and display the image preview at the bottom
                        uploadedImagePreview = document.createElement("img");
                        uploadedImagePreview.src = e.target.result; // Base64 image data
                        uploadedImagePreview.alt = "Uploaded Image";
                        uploadedImagePreview.style.maxWidth = "100%";
                        uploadedImagePreview.style.marginTop = "20px";
                        uploadedImagePreview.style.borderRadius = "8px";

                        // Append the image preview to the livePreview at the bottom
                        livePreview.append(uploadedImagePreview);
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert("Please upload a valid image file.");
                    customImageInput.value = ""; // Reset file input
                }
            }
        });

        // Update live preview when inputs change
        eventNameInput.addEventListener("input", () => {
            previewEventName.textContent = eventNameInput.value || "Event Name";
        });

        eventDescriptionInput.addEventListener("input", () => {
            previewEventDescription.textContent = eventDescriptionInput.value || "Event Description";
        });

        backgroundColorInput.addEventListener("input", () => {
            livePreview.style.backgroundColor = backgroundColorInput.value || "#ffffff";
        });

        // Close modal when the close button is clicked
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(closeButton => {
            closeButton.addEventListener('click', () => {
                inviteModal.hide();
                wishlistModal.hide();
                // console.log('Modal closed via close button'); // Debug log
            });
        });
        // Send the invite via AJAX
        document.getElementById("sendInviteButton").addEventListener("click", async () => {
            const eventId = document.getElementById("eventId").value;
            const email = document.getElementById("inviteEmail").value;
            const includeWishlist = document.getElementById("includeWishlistCheckbox").checked;

            const sendButton = document.getElementById('sendInviteButton');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Disable the send button and show the loading spinner
            sendButton.disabled = true;
            loadingSpinner.style.display = 'inline-block';

            const templateData = {
                event_name: eventNameInput.value,
                event_description: eventDescriptionInput.value,
                background_color: backgroundColorInput.value,
                color: fontColorInput.value,
                external_link: externalLinkInput.value,
            };

            if (!email) {
                displayInviteMessage("Email is required.");
                sendButton.disabled = false;
                loadingSpinner.style.display = 'none';
                return;
            }
            try {

                // Először lekérjük a kívánságlistát, ha be van pipálva az opció
                let wishlistData = null;
                if (includeWishlist) {
                    const wishlistResponse = await fetch(`getWishlist.php?event_id=${eventId}`);
                    if (wishlistResponse.ok) {
                        wishlistData = await wishlistResponse.json();
                    }
                }

                const response = await fetch("inviteHandler.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        email: email,
                        include_wishlist: includeWishlist,
                        wishlist_data: wishlistData,
                        template_data: templateData,
                    }),
                });
                const text = await response.text(); // Get raw text response

                /*

                FONTOS: Ha ilyesmi hiba van:

                Unexpected token '<', "<br />
                <fo"... is not valid JSON

                A megoldás, hogy kiíratod a nyers választ a szerverről!!! (felül)
                 */
                // const text = await response.text();
                // console.log("Raw response:", text);

                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
//                    console.error("Failed to parse JSON:", jsonError);
                    displayInviteMessage("Invalid server response.");
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
            finally {
                sendButton.disabled = false;
                loadingSpinner.style.display = 'none';
            }
        });
        // Display error message in the modal
        function displayInviteMessage(message) {
            const messageBox = document.getElementById("inviteMessage");
            messageBox.textContent = message;
            messageBox.style.display = "block";
        }



            // Wishlist modal //



        window.openWishlistModal = function (eventId) {
            document.getElementById("wishlistEventId").value = eventId;
            document.getElementById("wishlistItem").value = "";
            fetchWishlistItems(eventId);
            wishlistModal.show();
        };
// Fetch Wishlist Items
        async function fetchWishlistItems(eventId) {
            try {
                const response = await fetch("wishlistHandler.php?action=getItems&event_id=" + encodeURIComponent(eventId));
                if (!response.ok) throw new Error("Failed to fetch wishlist items.");
                const data = await response.json();
                renderWishlistItems(data.wishes); // Render items directly from JSON array
            } catch (error) {
                console.error("Error fetching wishlist items:", error);
                document.getElementById("wishlistItems").innerHTML = "<p>Failed to load wishlist items.</p>";
            }
        }
// Render Wishlist Items
        function renderWishlistItems(items) {
            const wishlistItemsDiv = document.getElementById("wishlistItems");
            wishlistItemsDiv.innerHTML = ""; // Clear existing items
            if (items.length > 0) {
                items.forEach((item) => {
                    const itemElement = document.createElement("div");
                    itemElement.className = "d-flex justify-content-between align-items-center mb-2";
                    itemElement.innerHTML = `
                <span>${item}</span>
                <button class="btn btn-sm btn-danger" onclick="removeWishlistItem('${item}')">Remove</button>
            `;
                    wishlistItemsDiv.appendChild(itemElement);
                });
            } else {
                wishlistItemsDiv.innerHTML = "<p>No items in the wishlist.</p>";
            }
        }
// Add Wishlist Item
        document.getElementById("addWishlistItemButton").addEventListener("click", async () => {
            const eventId = document.getElementById("wishlistEventId").value;
            const item = document.getElementById("wishlistItem").value;

            if (!item)
            {
                alert("Item name is required.");
                return;
            }

            try {
                const response = await fetch("wishlistHandler.php?action=addItem", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ event_id: eventId, item: item }),
                });
                const result = await response.json();
                if (result.success) {
                    fetchWishlistItems(eventId); // Reload the wishlist
                    document.getElementById("wishlistItem").value = ""; // Clear the input field
                } else {
                    alert("Error: " + result.error);
                }
            } catch (error) {
                console.error("Error adding wishlist item:", error);
                alert("Failed to add item.");
            }
        });
// Remove Wishlist Item
        window.removeWishlistItem = async function (item) {
            const eventId = document.getElementById("wishlistEventId").value;
            if (!confirm("Are you sure you want to remove this item?")) return;
            try {
                const response = await fetch("wishlistHandler.php?action=removeItem", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ event_id: eventId, item: item }),
                });
                const result = await response.json();
                if (result.success) {
                    fetchWishlistItems(eventId); // Reload the wishlist
                } else {
                    alert("Error: " + result.error);
                }
            } catch (error) {
                console.error("Error removing wishlist item:", error);
                alert("Failed to remove item. Please try again.");
            }
        };



                    // Save Profile part //



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



        // Functions



async function fetchUserProfile() {
    try {
        const baseurl1 = 'http://localhost/HWPProjektMarcello/PHP';
        const baseurl2 = 'http://localhost/HWP_2024/HWPProjektMARCELLO/PHP';
        const baseurlFinal = 'https://mmm.stud.vts.su.ac.rs/PHP'


        const url = baseurlFinal + '/api.php?action=getUserProfile&session_token=' + encodeURIComponent('<?php echo $sessionToken; ?>');
        // console.log('Requesting: ' + url);
        const response = await fetch(url);
        // console.log('Raw Response: ', response);
        if (!response.ok) {
            throw new Error('HTTP error: ' + response.status);
        }
        const textResponse = await response.text();
        // console.log('Raw JSON:', textResponse);
        const userProfile = JSON.parse(textResponse);
        // console.log('User Profile Response:', userProfile); // Log the entire response
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
</html>