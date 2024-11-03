<!-- header.php -->
<header>
	<nav class="navbar navbar-expand-md navbar-dark" style="background-color: #2E2E3A;">
		<div class="container">
			<!-- Logo and Brand Name -->
			<a class="navbar-brand" href="index.php" aria-description="Link to homepage">
				<img src="assets/pictures/logo.png" alt="Logo" style="max-width: 50px;" class="mr-2">
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
						<a class="nav-link" href="index.php">Home</a>
					</li>
					<!-- Services Dropdown -->
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Services
						</a>
						<div class="dropdown-menu" aria-labelledby="servicesDropdown" style="background-color: #2E2E3A;">
							<a class="dropdown-item footer-link" href="/hashtag-generator">Hashtag Generator</a>
							<a class="dropdown-item footer-link" href="/top-hashtags">Top Hashtags</a>
							<a class="dropdown-item footer-link" href="/photo-downloader">Photo/Video Downloader</a>
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
				</ul>
			</div>
		</div>
	</nav>
</header>

<!-- Include Bootstrap JS and jQuery -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<style>
    /* Navbar link styling */
    .nav-link {
        color: #BBB8B2 !important;
    }

    .nav-link:hover {
        color: #DE9151 !important;
    }

    /* Dropdown item styling */
    .dropdown-item.footer-link {
        color: #BBB8B2 !important;
    }

    .dropdown-item.footer-link:hover {
        color: #DE9151 !important;
        background-color: transparent;
    }
</style>
