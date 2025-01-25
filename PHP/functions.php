<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

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
		redirectToRegister("Please enter a valid phone number with format: (country code)[space](phone number), e.g.: 123 1234567890.", $_POST);
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

function loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass)
{
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT id_user, is_verified, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'This username (email) is not registered.'
            ];
        } else {
            if ($result['is_verified'] == 0) {
                return [
                    'success' => false,
                    'message' => 'This username (email) is registered but not yet verified.'
                ];
            }

            if (password_verify($password, $result['password'])) {
                //session_start();
                $sessionToken = bin2hex(random_bytes(32));
                $expiryDate = date("Y-m-d H:i:s", strtotime("+2 hours"));
                $userId = $result['id_user'];

                $stmt = $pdo->prepare("
                    INSERT INTO session_tokens (token, expiry_date, id_user) 
                    VALUES (:token, :expiry_date, :id_user)
                ");
                $stmt->bindParam(':token', $sessionToken);
                $stmt->bindParam(':expiry_date', $expiryDate);
                $stmt->bindParam(':id_user', $userId);
                $stmt->execute();

                $_SESSION['username'] = $username;
                $_SESSION['session_token'] = $sessionToken;

                return [
                    'success' => true,
                    'message' => 'Login successful!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'The password is incorrect. Please try again.'
                ];
            }
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES)
        ];
    } finally {
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
function redirectToLogin($message, $formData = [], $logged)
{
	$_SESSION['error'] = $message;
	$_SESSION['formData'] = $formData;
    if($logged == 1)
    {
        header("Location: login.php");
        exit();
    }
    else
    {
        header("Location: login.php");
        exit();
    }
}

function sendVerificationEmail($email, $token): void
{
	$mail = new PHPMailer(true);

	try
	{
		// Gábor part
		/*$mail->isSMTP();
		$mail->Host = 'sandbox.smtp.mailtrap.io';
		$mail->SMTPAuth = true;
		$mail->Port = 2525;
		$mail->Username = 'b9cb9fe9810051';
		$mail->Password = '84d8a60019f0f2';*/

		// Máté part
		$mail->isSMTP();
		$mail->Host = 'sandbox.smtp.mailtrap.io';
		$mail->SMTPAuth = true;
		$mail->Port = 2525;
		$mail->Username = 'd4a04c8e5deb9e';
		$mail->Password = 'bde0a6f4e281eb';

		// Email sender and recipient
		$mail->setFrom('mmmarcello@events.com', 'Marcello');
		$mail->addAddress($email);

		// Email content
		$mail->isHTML(true);
		$mail->Subject = 'Account Verification';
		$verificationLink = "http://localhost/index.php/PHP/verifyUser.php?token=$token";
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
		redirectToLogin("Please log in, to access this page.",null,1);
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
			redirectToLogin("Invalid attempt! Please log in again.",null,1);
			exit();
		}

		// Check if the token is expired
		if (strtotime($result['expiry_date']) < time())
		{
			// Token is expired; remove session and redirect
			session_unset();
			session_destroy();
			redirectToLogin("Your session has expired! Please log in again.",null,1);
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

function is_email_registered($email)
{
	global $pdo;

	$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :email");
	$stmt->execute(['email' => $email]);
	return $stmt->fetchColumn() > 0;
}

function save_password_reset_token($email, $token)
{
	global $pdo;

	$expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

	$stmt = $pdo->prepare("UPDATE users SET new_password_token = :token, new_password_token_expiry = :expires WHERE username = :email");
	$stmt->execute([
		'token' => $token,
		'expires' => $expires,
		'email' => $email
	]);
}

function send_password_reset_email($email, $token)
{
	$mail = new PHPMailer(true);

	try
	{
		// Gábor part
		/*$mail->isSMTP();
		$mail->Host = 'sandbox.smtp.mailtrap.io';
		$mail->SMTPAuth = true;
		$mail->Port = 2525;
		$mail->Username = 'b9cb9fe9810051';
		$mail->Password = '84d8a60019f0f2';*/

		// Máté part
		$mail->isSMTP();
		$mail->Host = 'sandbox.smtp.mailtrap.io';
		$mail->SMTPAuth = true;
		$mail->Port = 2525;
		$mail->Username = 'd4a04c8e5deb9e';
		$mail->Password = 'bde0a6f4e281eb';

		$mail->setFrom('mmmreset.noreply@gmail.com', 'MammaMiaMarcello');
		$mail->addAddress($email);

		$resetLink = "http://localhost:63342/index.php/PHP/reset_password.php?token=$token";
		$mail->isHTML(true);
		$mail->Subject = "Password Reset Request";
		$mail->Body    = "Click on the following link to reset your password: <a href='$resetLink'>$resetLink</a>";
		$mail->AltBody = "Click on the following link to reset your password: $resetLink";

		$mail->send();
		echo 'Password reset email sent.';
	}
	catch (Exception $e)
	{
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
}

function sendInviteEmail($email, $inviteToken, $inviter)
{
    $mail = new PHPMailer(true);

    try
    {
        // Gábor part
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ddd5c19228d753';
        $mail->Password = '138a3f6bfe0c20';

        // Máté part
        /*
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd4a04c8e5deb9e';
        $mail->Password = 'bde0a6f4e281eb';
        */

        $mail->setFrom('mmminvite.noreply@gmail.com', $inviter);
        $mail->addAddress($email);

        // Invitation links (Gábor)
        $acceptLink = "http://localhost/HWPProjektMARCELLO/PHP/logged_in_sites/invitation_statusHandler.php?action=accept&token={$inviteToken}";
        $declineLink = "http://localhost/HWPProjektMARCELLO/PHP/logged_in_sites/invitation_statusHandler.php?action=decline&token={$inviteToken}";
        $dontKnowLink = "http://localhost/HWPProjektMARCELLO/PHP/logged_in_sites/invitation_statusHandler.php?action=dontknow&token={$inviteToken}";

        // Invitation links (Máté)


        // HTML version of the email
        $mail->isHTML(true);
        $mail->Subject = "You have been invited to an event!";
        $mail->Body = "
            <html lang='en'>
            <body>
                <h2>You are invited to a special event!</h2>
                <p>We are excited to invite you to join us for a memorable event.</p>
                <p>To RSVP, please click one of the options below:</p>
                <p><a href='{$acceptLink}'>Accept Invitation</a></p>
                <p><a href='{$declineLink}'>Decline Invitation</a></p>
                <p><a href='{$dontKnowLink}'>Not Sure Yet</a></p>
                <p>Looking forward to seeing you!</p>
                <p>Best Regards,</p>
                <p>The MammaMiaMarcello Team</p>
            </body>
            </html>
        ";

        // Plain text version of the email (alternative body)
        $mail->AltBody = "
            You are invited to a special event!\n\n
            We are excited to invite you to join us for a memorable event.\n\n
            To RSVP, please click one of the options below:\n\n
            Accept Invitation: {$acceptLink}\n\n
            Decline Invitation: {$declineLink}\n\n
            Not Sure Yet: {$dontKnowLink}\n\n
            Looking forward to seeing you!\n\n
            Best Regards,\n
            The MammaMiaMarcello Team
        ";

        $mail->send();
    }
    catch (Exception $e)
    {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}