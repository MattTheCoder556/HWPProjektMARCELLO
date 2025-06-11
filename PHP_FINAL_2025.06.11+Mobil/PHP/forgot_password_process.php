<?php
require_once "config.php";
require_once "functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$email = $_POST['email'] ?? '';

	if (!empty($email))
	{
		// Check if the email is registered
		if (is_email_registered($email))
		{
			// Generate a unique token and save it to the database with an expiration time
			$token = bin2hex(random_bytes(16));
			save_password_reset_token($email, $token);

			// Send password reset email
			send_password_reset_email($email, $token);

			$_SESSION['message'] = 'A password reset link has been sent to your email address.';
		}
		else
		{
			$_SESSION['message'] = 'Email address not found.';
		}
	}
	else
	{
		$_SESSION['message'] = 'Please enter a valid email address.';
	}
	header("Location: forgot_password.php");
	exit;
}
