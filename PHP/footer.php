<div class="row">
    <!-- Company Column -->
    <div class="col-12 col-md-4 mb-4">
        <h6 class="fw-bold" style="color: #DE9151;">Main</h6>
        <ul class="list-unstyled">
            <li><a href="index.php" class="footer-link">Home page</a></li>
            <?php
            if(!isset($_SESSION['username']) && !isset($_SESSION['session_token']))
            {
                echo '<li><a href="register.php" class="footer-link">Register</a></li>';
                echo '<li><a href="login.php" class="footer-link">Login</a></li>';
            }
            ?>
        </ul>
    </div>

    <!-- Services Column -->
    <div class="col-12 col-md-4 mb-4">
        <h6 class="fw-bold" style="color: #F34213;">Sites</h6>
        <ul class="list-unstyled">
            <?php
            if(isset($_SESSION['username']) && isset($_SESSION['session_token']))
            {
                echo '<li><a href="logged_in_sites/eventMaker.php" class="footer-link">Event maker</a></li>';
                echo '<li><a href="logged_in_sites/profileMain.php" class="footer-link">Profile</a></li>';
            }
            ?>
            <li><a href="availableEvents.php" class="footer-link">Available Events</a></li>
        </ul>
    </div>

    <!-- Legal Column -->
    <div class="col-12 col-md-4 mb-4">
        <h6 class="fw-bold" style="color: #BC5D2E;">Other</h6>
        <ul class="list-unstyled">
            <li><a href="contact.php" class="footer-link">Contact</a></li>
            <li><a href="faq.php" class="footer-link">FAQ</a></li>
        </ul>
    </div>
</div>
</div>
</div>

<!-- Download Buttons -->
<div class="row text-center mt-4">
    <?php if ($detect->isiOS()): ?>
        <div class="col-12">
            <a href="<?php echo $ios_link; ?>" class="btn btn-primary" style="background-color: #007AFF; border-color: #007AFF;">
                <i class="fa fa-apple"></i> Download on the App Store
            </a>
        </div>
    <?php elseif ($detect->isAndroidOS()): ?>
        <div class="col-12">
            <a href="<?php echo $android_link; ?>" class="btn btn-success" style="background-color: #3DDC84; border-color: #3DDC84;">
                <i class="fa fa-android"></i> Get it on Google Play
            </a>
        </div>
    <?php else: ?>
        <div class="col-12">
            <p class="text-muted">Download our app on iOS or Android devices too!</p>
        </div>
    <?php endif; ?>
</div>

<!-- Divider and Bottom Section -->
<hr class="my-4" style="border-color: #BBB8B2;">
<div class="text-center">
    <img src="../assets/pictures/logo.png" alt="Logo" style="max-width: 50px;" class="mb-2">
    <p class="mb-0" style="color: #DE9151;">&copy; 2025 MammaMia Marcello Event Organizer - All rights reserved.</p>
</div>
</div>
</footer>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<style>
    .footer-link {
        color: #BBB8B2;
        text-decoration: none;
    }
    .footer-link:hover {
        color: #DE9151;
    }
</style>
