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
<form action="login_process.php" method="POST">
	<label for="username">Username:</label>
	<input type="text" name="username" required>
	<label for="password">Password:</label>
	<input type="password" name="password" required>
	<button type="submit">Login</button>
</form>
</body>
</html>
