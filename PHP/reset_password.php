<?php
require_once "config.php";
require_once "functions.php";

$token = $_GET['token'] ?? '';
$error = '';

if (!$token) {
	$error = "Invalid or missing token.";
} else {
	try {
		$stmt = $pdo->prepare("SELECT username, new_password_token_expiry FROM users WHERE new_password_token = :token");
		$stmt->execute(['token' => $token]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$user) {
			$error = "Invalid token.";
		} elseif (new DateTime() > new DateTime($user['new_password_token_expiry'])) {
			$error = "Token has expired, please generate a new one.";
		} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$newPassword = $_POST['new_password'] ?? '';
			$confirmPassword = $_POST['confirm_password'] ?? '';

			if ($newPassword !== $confirmPassword) {
				$error = "Passwords do not match.";
			} elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $newPassword)) {
				$error = "Password must be at least 8 characters long, contain at least one uppercase letter, and at least one number.";
			} else {
				$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
				$updateStmt = $pdo->prepare("UPDATE users SET password = :password, new_password_token = NULL, new_password_token_expiry = NULL WHERE new_password_token = :token");
				$updateStmt->execute([
					'password' => $hashedPassword,
					'token' => $token
				]);

				echo "<script>alert('Password has been reset successfully.'); window.location.href = 'login.php';</script>";
				exit;
			}
		}
	} catch (Exception $e) {
		$error = "An error occurred: " . $e->getMessage();
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reset Password</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background-color: #2E2E3A">
<a href="index.php" target="_self">
	<button id="backButton" class="btn btn-light mb-3" style="position: absolute; top: 20px; left: 20px;">
		<svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
			<path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
		</svg>
		<span>Home</span>
	</button>
</a>

<?php if ($error): ?>
	<script>alert('<?php echo htmlspecialchars($error, ENT_QUOTES); ?>');</script>
<?php endif; ?>

<div class="container mt-5 p-4 border rounded shadow-sm text-white" style="background-color: #333333; max-width: 500px;">
	<h2 class="text-center mb-4">Reset Password</h2>
	<form method="POST">
		<div class="form-group">
			<label for="new_password">New Password:</label>
			<input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required>
		</div>
		<div class="form-group">
			<label for="confirm_password">Confirm New Password:</label>
			<input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
		</div>
		<button type="submit" class="btn btn-block" style="background-color: #F34213;">Reset Password</button>
	</form>
</div>
</body>
</html>
