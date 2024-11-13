<?php
session_start();

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
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/reg_log.css">
</head>
<body>
<h2>Register</h2>

<!-- Display the error message if available -->
<?php if ($error): ?>
    <script>alert('<?php echo htmlspecialchars($error, ENT_QUOTES); ?>');</script>
<?php endif; ?>

<!-- Registration form -->
<form action="register_process.php" method="POST">
    <label for="firstname">First Name:</label>
    <input type="text" name="firstname" placeholder="Max 40 characters" value="<?php echo htmlspecialchars($formData['firstname'] ?? ''); ?>" required>

    <label for="lastname">Last Name:</label>
    <input type="text" name="lastname" placeholder="Max 40 characters" value="<?php echo htmlspecialchars($formData['lastname'] ?? ''); ?>" required>

    <label for="username">Username (Email):</label>
    <input type="email" name="username" placeholder="name@example.com" value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>" required>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" placeholder="(123) 456-7890" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" required>

    <label for="password">Password:</label>
    <input type="password" name="password" placeholder="8+ chars, 1 uppercase, 1 number" required>

    <button type="submit">Register</button>
</form>

</body>
</html>
