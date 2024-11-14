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
		redirectToRegister("First name cannot exceed " . $maxLengths['firstname'] . " characters.", $_POST);
	}

	if (strlen($lastName) > $maxLengths['lastname'])
	{
		redirectToRegister("Last name cannot exceed " . $maxLengths['lastname'] . " characters.", $_POST);
	}

	if (!filter_var($username, FILTER_VALIDATE_EMAIL) || strlen($username) > $maxLengths['username'])
	{
		redirectToRegister("Please enter a valid email address (maximum " . $maxLengths['username'] . " characters).", $_POST);
	}

	if (!preg_match('/^\d{1,3} \d{7,12}$/', $phone))
	{
		redirectToRegister("Please enter a valid phone number with format: (country code)[space](phone number), e.g., '123 1234567890'.", $_POST);
	}

	if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password))
	{
		redirectToRegister("Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number.", $_POST);
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
			redirectToRegister("The username (email) is already registered.", $_POST);
		}

		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$registration_token = bin2hex(random_bytes(16));
		$expiry_date = date("Y-m-d H:i:s", strtotime("+2 hours"));

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

		// Insert registration token into registration_tokens table
		$stmt = $pdo->prepare("
            INSERT INTO registration_tokens (username, expiry_date, reg_token)
            VALUES (:username, :expiry_date, :reg_token)
        ");
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':expiry_date', $expiry_date);
		$stmt->bindParam(':reg_token', $registration_token);
		$stmt->execute();

		echo "<script>
        	alert('Registration successful! Please verify your email to activate your account.');
        	window.location.href = 'login.php';
    	</script>";

		// Verification email
		sendVerificationEmail($username, $registration_token);
	}
	catch (PDOException $e)
	{
		redirectToRegister("Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES), $_POST);
	}
	finally
	{
		$pdo = null;
	}
}

function loginUser(string $username, string $password, string $dbHost, string $dbName, string $dbUser, string $dbPass)
{
	try
	{
		$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Check if username (email) is registered and fetch the is_verified status
		$stmt = $pdo->prepare("SELECT id_user,is_verified,password FROM users WHERE username = :username");
		$stmt->bindParam(':username', $username);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result)
		{
			redirectToLogin("This username (email) is not registered.", $_POST);
			exit();
		}
		else
		{
			// Check if the account is verified
			if ($result['is_verified'] == 0)
			{
				redirectToLogin("This username (email) is registered but not yet verified.", $_POST);
			}
			if (password_verify($password, $result['password']))
			{
				session_start();

				$sessionToken = bin2hex(random_bytes(32));
				$expiryDate = date("Y-m-d H:i:s", strtotime("+2 hours"));
				$userId = $result['id_user'];

				$stmt = $pdo->prepare("
                INSERT INTO session_tokens (token, expiry_date, id_user) VALUES (:token, :expiry_date, :id_user)");
				$stmt->bindParam(':token', $sessionToken);
				$stmt->bindParam(':expiry_date', $expiryDate);
				$stmt->bindParam(':id_user', $userId);
				$stmt->execute();

				$_SESSION['username'] = $username;
				$_SESSION['session_token'] = $sessionToken;

				header('Location: event.php');
				exit();
			}
			else
			{
				redirectToLogin("The password is incorrect. Please try again.", $_POST);
			}
		}
	}
	catch (PDOException $e)
	{
		redirectToRegister("Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES), $_POST);
	}
	finally
	{
		$pdo = null;
	}
}

function redirectToRegister($message, $formData = [])
{
	$_SESSION['error'] = $message;
	$_SESSION['formData'] = $formData;
	header("Location: register.php");
	exit();
}
function redirectToLogin($message, $formData = [])
{
	$_SESSION['error'] = $message;
	$_SESSION['formData'] = $formData;
	header("Location: login.php");
	exit();
}

function sendVerificationEmail($email, $token): void
{
	$mail = new PHPMailer(true);

	try
	{
		// Gábor part
		$mail->isSMTP();
		$mail->Host = 'sandbox.smtp.mailtrap.io';
		$mail->SMTPAuth = true;
		$mail->Port = 2525;
		$mail->Username = 'b9cb9fe9810051';
		$mail->Password = '84d8a60019f0f2';

		// Máté part


		// Email sender and recipient
		$mail->setFrom('mmmarcello@events.com', 'Marcello');
		$mail->addAddress($email);

		// Email content
		$mail->isHTML(true);
		$mail->Subject = 'Account Verification';
		$verificationLink = "http://localhost:63342/index.php/PHP/verifyUser.php?token=$token";
		$mail->Body = "<h1>Account Verification</h1>
                       <p>Click the link below to verify your account:</p>
                       <a href='$verificationLink'>Verify me!</a>
                       <p>If you did not request this registration, please ignore this email.</p>";

		$mail->AltBody = "Account Verification\n\n"
		                 . "Please click the link below to verify your account:\n"
		                 . "Verify me!\n\n"
		                 . "If you did not request this registration, please ignore this email.";

		$mail->send();
	}
	catch (Exception $e)
	{
		redirectToRegister("Mailer Error: " . htmlspecialchars($mail->ErrorInfo, ENT_QUOTES));
	}
}

function tokenVerify(string $dbHost, string $dbName, string $dbUser, string $dbPass)
{

	// Check if the username and session token exist in the session
	if (!isset($_SESSION['username']) || !isset($_SESSION['session_token']))
	{
		// Missing session details; redirect to login page with a message
		redirectToLogin("Please register/log in to access this page.");
		exit();
	}

	$username = $_SESSION['username'];
	$sessionToken = $_SESSION['session_token'];

	try
	{
		$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Check if the session token is valid and not expired
		$stmt = $pdo->prepare("SELECT st.expiry_date FROM session_tokens AS st
            						JOIN users AS u ON st.id_user = u.id_user
            						WHERE u.username = :username AND st.token = :token");
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':token', $sessionToken);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if a matching session token was found
		if (!$result)
		{
			// Invalid token; redirect to login page
			redirectToLogin("Invalid attempt! Please log in again.",null);
			exit();
		}

		// Check if the token is expired
		if (strtotime($result['expiry_date']) < time())
		{
			// Token is expired; remove session and redirect
			session_unset();
			session_destroy();
			redirectToLogin("Your session has expired! Please log in again.",null);
			exit();
		}

		// Token is valid and not expired; user is authenticated -> Proceed with the page load

	}
	catch (PDOException $e)
	{
		echo "Error: " . htmlspecialchars($e->getMessage());
		exit();
	}
	finally
	{
		$pdo = null;
	}
}
