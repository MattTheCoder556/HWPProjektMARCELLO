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
</head>
<body>
<h2>Login</h2>
<!-- Display the error message if available -->
<?php if ($error): ?>
    <script>alert('<?php echo htmlspecialchars($error, ENT_QUOTES); ?>');</script>
<?php endif; ?>
<form action="login_process.php" method="POST">
	<label for="username">Username:</label>
	<input type="text" name="username" value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>" required>
	<label for="password">Password:</label>
	<input type="password" name="password" required>
	<button type="submit">Login</button>
</form>
</body>
</html>
