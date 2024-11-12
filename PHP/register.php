<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register</title>
	<link rel="stylesheet" href="../assets/css/reg_log.css">
</head>
<body>
<h2>Register</h2>
<form action="register_process.php" method="POST">
	<label for="username">Username:</label>
	<input type="text" name="username" required>
	<label for="password">Password:</label>
	<input type="password" name="password" required>
	<button type="submit">Register</button>
</form>
</body>
</html>
