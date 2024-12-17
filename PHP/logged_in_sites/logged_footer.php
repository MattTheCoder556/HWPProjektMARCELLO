<!-- footer.php -->
<?php
require __DIR__ . "/../../vendor/autoload.php";
use Detection\MobileDetect;
$detect = new MobileDetect();

$ios_link = "https://apps.apple.com/app/idYOUR_APP_ID";
$android_link = "https://play.google.com/store/apps/details?id=YOUR_APP_PACKAGE";

?>
<footer class="py-5" style="background-color: #2E2E3A; color: #BBB8B2;">
    <div class="container">
        <div class="row text-center text-md-start align-items-start">
            <!-- Logo -->
            <div class="col-sm col-md-3 mb-4 text-center text-md-start">
                <a href="../index.php" target="_self" aria-description="Link to indexpage"><img src="../../assets/pictures/logo.png" alt="Logo" class="mb-2" style="max-width: 120px;"></a>
            </div>

            <!-- Company, Services, and Legal Columns in a Row -->
            <div class="col-12 col-md-9">
                <div class="row">
                    <!-- Company Column -->
                    <div class="col-12 col-md-4 mb-4">
                        <h6 class="fw-bold" style="color: #DE9151;">Company</h6>
                        <ul class="list-unstyled">
                            <li><a href="/faq" class="footer-link">FAQ</a></li>
                            <li><a href="/tutorials" class="footer-link">Tutorials</a></li>
                        </ul>
                    </div>

                    <!-- Services Column -->
                    <div class="col-12 col-md-4 mb-4">
                        <h6 class="fw-bold" style="color: #F34213;">Services</h6>
                        <ul class="list-unstyled">
                            <?php
                                if(isset($_SESSION['username']) && isset($_SESSION['session_token']))
                                {
                                    echo '<li><a href="eventMaker.php" class="footer-link">Event maker</a></li>';
                                    echo '<li><a href="profileMain.php" class="footer-link">Profile</a></li>';
                                }
                            ?>
                            <li><a class="dropdown-item footer-link" href="../availableEvents.php">Available events</a></li>
                        </ul>
                    </div>

                    <!-- Legal Column -->
                    <div class="col-12 col-md-4 mb-4">
                        <h6 class="fw-bold" style="color: #BC5D2E;">Legal</h6>
                        <ul class="list-unstyled">
                            <li><a href="/terms" class="footer-link">Terms of Service</a></li>
                            <li><a href="/privacy-policy" class="footer-link">Privacy Policy</a></li>
                            <li><a href="/billing-info" class="footer-link">Billing Information</a></li>
                            <li><a href="/refund-policy" class="footer-link">Refund & Cancellation Policy</a></li>
                            <li><a href="/cookie-consent" class="footer-link">Cookie Consent</a></li>
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
            <img src="../../assets/pictures/logo.png" alt="Logo" style="max-width: 50px;" class="mb-2">
            <p class="mb-0" style="color: #DE9151;">&copy; 2024 MammaMia Marcello Event Organizer - All rights reserved.</p>
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
    .footer-link:visited {
        color: #BBB8B2;
    }
</style>
