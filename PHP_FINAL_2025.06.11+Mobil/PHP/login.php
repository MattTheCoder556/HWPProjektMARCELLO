<?php
require_once "config.php";
require_once "functions.php";

// Retrieve error message and form data from session, if available
$error = $_SESSION['error'] ?? '';
$formData = $_SESSION['formData'] ?? [];

// Clear session data to prevent persistent errors on page reload
unset($_SESSION['error'], $_SESSION['formData']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login</title>
	<link rel="stylesheet" href="../assets/css/reg_log.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<a href="../index.php" target="_self">
    <button id="backButton">
        <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024"><path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path></svg>
        <span>Home</span>
    </button>
</a>
<!-- Display the error message if available -->
<?php if ($error): ?>
    <script>alert('<?php echo htmlspecialchars($error, ENT_QUOTES); ?>');</script>
<?php endif; ?>
<form action="login_process.php" method="POST" class="container mt-5 p-4 border rounded shadow-sm text-white" style="max-width: 500px;">
    <h2 class="text-center mb-4">Login Form</h2>
    <div class="form-group">
        <label for="username">Username (Email):</label>
        <input type="email" class="form-control" name="username" placeholder="name@example.com"
               value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" class="form-control" name="password" placeholder="Your password" required>
    </div>
    <button type="submit" class="btn btn-block reg_log_button" style="background-color: #F34213;">Login</button>
    <div class="text-center mt-3">
        <a href="forgot_password.php" class="text-white" style="background: #BC5D2E; padding: 0.5em; border-radius: 4px;">Forgot Password?</a>
    </div>
    <br>
    <div class="text-center mt-3">
        <p>No account yet?</p>
        <a href="register.php" class="text-white" style="background: #BC5D2E; padding: 0.5em; border-radius: 4px;">Register!</a>
    </div>
</form>
</body>
</html>
