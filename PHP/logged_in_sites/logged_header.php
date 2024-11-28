<header>
	<nav class="navbar navbar-expand-md navbar-dark" style="background-color: #2E2E3A;">
		<div class="container">
			<!-- Logo and Brand Name -->
			<a class="navbar-brand" href="../index.php" aria-description="Link to homepage">
				<img src="../../assets/pictures/logo.png" alt="Logo" style="max-width: 50px;" class="mr-2">
				<span style="color: #DE9151;">MammaMia Marcello</span>
			</a>

			<!-- Toggler for Mobile View -->
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<!-- Navbar Links -->
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ml-auto">
					<!-- Home -->
					<li class="nav-item">
						<a class="nav-link" href="../index.php">Home</a>
					</li>
					<!-- Services Dropdown -->
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Sites
						</a>
						<div class="dropdown-menu" aria-labelledby="servicesDropdown" style="background-color: #2E2E3A;">
							<?php
							require_once '../functions.php';

							if(isset($_SESSION['username']) && isset($_SESSION['session_token']))
                            {
							    echo '<a class="dropdown-item footer-link" href="eventMaker.php">Event maker</a>';
							    echo '<a class="dropdown-item footer-link" href="profileMain.php">Profile</a>';
							}
							?>
                            <a class="dropdown-item footer-link" href="../availableEvents.php">Available events</a>
							<a class="dropdown-item footer-link" href="/analyze-name">Analyze by Name</a>
							<a class="dropdown-item footer-link" href="/search-profiles">Search Instagram Profiles</a>
						</div>
					</li>
					<!-- FAQ -->
					<li class="nav-item">
						<a class="nav-link" href="/faq">FAQ</a>
					</li>
					<!-- Contact -->
					<li class="nav-item">
						<a class="nav-link" href="/contact">Contact</a>
					</li>
					<?php
                    require_once "../functions.php";
                        if (!isset($_SESSION['username']) || !isset($_SESSION['session_token'])): ?>
                            <!-- Display Register and Login buttons if the user is not logged in -->
                            <li>
                                <button onclick="window.location.href='register.php'" class="btn" id="reg_button" style="background-color: #BC5D2E">Register</button>
                            </li>
                        <li style="visibility: hidden">""</li>
                            <li>
                                <button onclick="window.location.href='login.php'" class="btn" id="log_button" style="background-color: #DE9151">Login</button>
                            </li>
                            <?php
                            else: ?>
                            <!-- Display Logout button if the user is logged in -->
                            <li>
                                <button onclick="window.location.href='logout.php'" class="btn btn-danger">Logout</button>
                            </li>
                    <?php endif; ?>
                </ul>
			</div>
		</div>
	</nav>
</header>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<style>
    .nav-link {
        color: #BBB8B2 !important;
    }

    .nav-link:hover {
        color: #DE9151 !important;
    }

    .dropdown-item.footer-link {
        color: #BBB8B2 !important;
    }

    .dropdown-item.footer-link:hover {
        color: #DE9151 !important;
        background-color: transparent;
    }
    #reg_button:hover {
        box-shadow: 9px 9px 33px #BBB8B2, -9px -9px 33px #BBB8B2 !important;
        border: 1px solid #BBB8B2 !important;
    }
    #log_button:hover {
        box-shadow: 9px 9px 33px #BBB8B2, -9px -9px 33px #BBB8B2 !important;
        border: 1px solid #BBB8B2 !important;
    }
</style>
