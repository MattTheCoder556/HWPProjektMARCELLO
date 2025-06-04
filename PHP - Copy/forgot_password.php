<?php
require_once "config.php";
require_once "functions.php";

// Retrieve error or success message from session, if available
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']); // Clear message after displaying
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            background-image: url('../assets/pictures/logo.png');
            background-size: cover;
            background-position: center;
            max-width: 500px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-block {
            display: block;
            width: 100%;
            text-align: center;
        }

        #backButton span {
            white-space: nowrap;
        }

        .form-group label {
            color: white;
        }

        .alert-info {
            color: #333;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background-color: #2E2E3A;">

<a href="login.php" target="_self">
    <button id="backButton" class="btn btn-light mb-3" style="position: absolute; top: 20px; left: 20px;">
        <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
            <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
        </svg>
        <span>Login</span>
    </button>
</a>

<div class="container mt-5 p-4 border rounded shadow-sm text-white form-container">
    <h2 class="text-center mb-4">Forgotten Password</h2>

    <!-- Display the message if available -->
	<?php if ($message): ?>
        <div class="alert alert-info text-center">
			<?php echo htmlspecialchars($message); ?>
        </div>
	<?php endif; ?>

    <form action="forgot_password_process.php" method="POST">
        <div class="form-group">
            <label for="email">Enter your email address:</label>
            <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
        </div>

        <button type="submit" class="btn btn-block" style="background-color: #F34213; color: white;">Request Password Reset</button>
    </form>
</div>

</body>
</html>
