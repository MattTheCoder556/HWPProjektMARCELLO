<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function registerUser( string $firstName, string $lastName, string $username, string $phone, string $password, string $dbHost, string $dbName, string $dbUser, string $dbPass )
{
	// Character limits based on database
	$maxLengths = [
		'firstname' => 40,
		'lastname' => 40,
		'username' => 50
	];

	// Sanitize and validate each input
	if (strlen($firstName) > $maxLengths['firstname'])
	{
		redirectWithAlert("First name cannot exceed " . $maxLengths['firstname'] . " characters.", $_POST);
	}

	if (strlen($lastName) > $maxLengths['lastname'])
	{
		redirectWithAlert("Last name cannot exceed " . $maxLengths['lastname'] . " characters.", $_POST);
	}

	if (!filter_var($username, FILTER_VALIDATE_EMAIL) || strlen($username) > $maxLengths['username'])
	{
		redirectWithAlert("Please enter a valid email address (maximum " . $maxLengths['username'] . " characters).", $_POST);
	}

	if (!preg_match('/^\d{1,3} \d{7,12}$/', $phone))
	{
		redirectWithAlert("Please enter a valid phone number with format: (country code)[space](phone number), e.g., '123 1234567890'.", $_POST);
	}

	if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password))
	{
		redirectWithAlert("Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number.", $_POST);
	}

	try
	{
		$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Check if username (email) already exists
		$stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = :username");
		$stmt->bindParam(':username', $username);
		$stmt->execute();

		if ($stmt->rowCount() > 0)
		{
			redirectWithAlert("The username (email) is already registered.", $_POST);
		}

		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$registration_token = bin2hex(random_bytes(16));

		// Insert the new user into the database
		$stmt = $pdo->prepare("
        INSERT INTO users (firstname, lastname, username, phone, password, is_verified, is_banned, registration_token)
        VALUES (:firstname, :lastname, :username, :phone, :password, 0, 0, :registration_token)
    	");
		$stmt->bindParam(':firstname', $firstName);
		$stmt->bindParam(':lastname', $lastName);
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':phone', $phone);
		$stmt->bindParam(':password', $hashed_password);
		$stmt->bindParam(':registration_token', $registration_token);

		$stmt->execute();

		echo "<script>
        	alert('Registration successful! Please verify your email to activate your account. Token: $registration_token');
        	window.location.href = 'login.php';
    	</script>";

		// Verification email
		sendVerificationEmail($username, $registration_token);
	}
	catch (PDOException $e)
	{
		redirectWithAlert("Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES), $_POST);
	}
	finally
	{
		$pdo = null;
	}
}

function loginUser()
{

}

function redirectWithAlert($message, $formData = [])
{
	$_SESSION['error'] = $message;
	$_SESSION['formData'] = $formData;
	header("Location: register.php");
	exit();
}

function sendVerificationEmail($email, $token): void
{
	$mail = new PHPMailer(true);

	try
	{
		$mail->isSMTP();
		$mail->isSMTP();
		$mail->Host = 'sandbox.smtp.mailtrap.io';
		$mail->SMTPAuth = true;
		$mail->Port = 2525;
		$mail->Username = 'b9cb9fe9810051';
		$mail->Password = '84d8a60019f0f2';

		// Email sender and recipient
		$mail->setFrom('mmmarcello@events.com', 'Marcello');
		$mail->addAddress($email);

		// Email content
		$mail->isHTML(true);
		$mail->Subject = 'Account Verification';
		$verificationLink = "http://yourwebsite.com/verify.php?token=$token";
		$mail->Body = "<h1>Account Verification</h1>
                       <p>Click the link below to verify your account:</p>
                       <a href='$verificationLink'>$verificationLink</a>
                       <p>If you did not request this registration, please ignore this email.</p>";

		$mail->AltBody = "Account Verification\n\n"
		                 . "Please click the link below to verify your account:\n"
		                 . "$verificationLink\n\n"
		                 . "If you did not request this registration, please ignore this email.";

		$mail->send();
		echo 'Verification email has been sent.';
	}
	catch (Exception $e)
	{
		redirectWithAlert("Mailer Error: " . htmlspecialchars($mail->ErrorInfo, ENT_QUOTES));
	}
}