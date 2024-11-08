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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
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
