<?php
include_once 'logged_header.php';
require_once '../functions.php';
require_once '../config.php';

$sessionToken = $_SESSION['session_token'];
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <title>Manage Event Invites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>

<header class="p-3 text-center bg-dark text-white">
    <h1>Manage Event Invites</h1>
</header>

<div class="container mt-5">
    <div class="card p-4 mb-4">
        <h2 class="mb-3">Invited People</h2>
        <table class="table table-bordered">
            <thead class="table-dark">
            <tr>
                <th>Invitee</th>
                <th>Status</th>
                <th>Invited By</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="inviteList">
            <tr><td colspan="4" class="text-center">Loading invites...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Invite Modal -->
<div class="modal fade" id="editInviteModal" tabindex="-1" aria-labelledby="editInviteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Invite</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editInviteId">
                <div class="mb-3">
                    <label for="editInviteStatus" class="form-label">Status</label>
                    <select id="editInviteStatus" class="form-select">
                        <option value="accepted">Accepted</option>
                        <option value="declined">Declined</option>
                        <option value="dontknow">Not Sure Yet</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveInviteChanges" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<?php include_once "logged_footer.php"?>
</body>
<!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        fetchInvites();

        async function fetchInvites() {
            try {
                const eventId = 11;
                const baseurl1 = 'http://localhost/HWPProjektMarcello/PHP/api/';
                const baseurl2 = 'http://localhost/HWP_2024/MammaMiaMarcello/PHP/api/';
                const response = await fetch(baseurl1 + 'getInvites?event_id=' + eventId);
                if (!response.ok) throw new Error("Failed to fetch invites.");
                const invites = await response.json();
                console.log("Fetched Invites:", invites);
                renderInvites(invites);
            } catch (error) {
                console.error("Error fetching invites:", error);
                document.getElementById("inviteList").innerHTML = "<tr><td colspan='4' class='text-center'>Failed to load invites.</td></tr>";
            }
        }

        function renderInvites(invites) {
            const inviteList = document.getElementById("inviteList");
            inviteList.innerHTML = "";

            if (invites.length > 0) {
                invites.forEach(invite => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                    <td>${invite.username}</td>
                    <td><span class="badge bg-secondary">${invite.status || "Pending"}</span></td>
                    <td>${invite.invited_by}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="openEditInviteModal(${invite.id_event_invite}, '${invite.status}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteInvite(${invite.id_event_invite})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                `;
                    inviteList.appendChild(row);
                });
            } else {
                inviteList.innerHTML = "<tr><td colspan='4' class='text-center'>No invites found.</td></tr>";
            }
        }

        window.openEditInviteModal = function (inviteId, status) {
            document.getElementById("editInviteId").value = inviteId;
            document.getElementById("editInviteStatus").value = status;
            new bootstrap.Modal(document.getElementById("editInviteModal")).show();
        };

        document.getElementById("saveInviteChanges").addEventListener("click", async () => {
            const inviteId = document.getElementById("editInviteId").value;
            const newStatus = document.getElementById("editInviteStatus").value;

            try {
                const response = await fetch("api.php?action=updateInvite", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id_event_invite: inviteId, status: newStatus })
                });

                const result = await response.json();
                if (result.success) {
                    fetchInvites();
                    bootstrap.Modal.getInstance(document.getElementById("editInviteModal")).hide();
                } else {
                    alert("Error updating invite: " + result.error);
                }
            } catch (error) {
                console.error("Error updating invite:", error);
            }
        });

        window.deleteInvite = async function (inviteId) {
            if (!confirm("Are you sure you want to delete this invite?")) return;

            try {
                const response = await fetch("api.php?action=deleteInvite", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id_event_invite: inviteId })
                });

                const result = await response.json();
                if (result.success) {
                    fetchInvites();
                } else {
                    alert("Error deleting invite: " + result.error);
                }
            } catch (error) {
                console.error("Error deleting invite:", error);
            }
        };
    });
</script>
</html>
