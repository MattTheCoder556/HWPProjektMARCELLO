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

function loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass): array
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
				//echo $userId;

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
                    'message' => 'Login successful!',
					'user_id' => $userId,
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

function sendInviteEmail($email, $inviteToken, $inviter, $wishlistHtml = "", $templateData): void
{
    $mail = new PHPMailer(true);

    try
    {
        // Gábor part
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ddd5c19228d753';
        $mail->Password = '138a3f6bfe0c20';

        // Máté part
        /*
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd4a04c8e5deb9e';
        $mail->Password = 'bde0a6f4e281eb';
        */

        $mail->setFrom('mmminvite.noreply@gmail.com', $inviter);
        $mail->addAddress($email);

        // Fetch template data
        $eventName = $templateData['event_name'] ?? "Event Name";
        $eventDescription = $templateData['event_description'] ?? "Event Description";
        $backgroundColor = $templateData['background_color'] ?? "#ffffff";
        $fontColor = $templateData['color'] ?? "#000000";
        $externalLink = $templateData['external_link'] ?? "";
        $imageUrl = $templateData['uploaded_image_url'] ?? "";

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
            <body style='background-color: {$backgroundColor}; font-family: Arial, sans-serif; color: {$fontColor}; padding: 20px;'>
                <h2 style='color: {$fontColor}; text-align: center;'>{$eventName}</h2>
                <p style='font-size: 16px;'>{$eventDescription}</p>
                " . ($externalLink ? "<p><a href='{$externalLink}' style='color: {$fontColor};'>Visit the extra link</a></p>" : "") . "
                <div style='text-align: center; margin-top: 40px;'>
                    " . ($imageUrl ? "<img src='{$imageUrl}' alt='Event Image' style='max-width: 100%; border-radius: 8px; margin-bottom: 20px;' />" : "") . "
                </div>
                <p style='font-size: 16px;'>To RSVP, please click one of the options below:</p>
                <p style='text-align: center; margin-bottom: 60px; margin-top: 20px;'>
                    <a href='{$acceptLink}' style='text-decoration: none; color: white; background-color: #4CAF50; padding: 10px 20px; border-radius: 5px;'>Accept Invitation</a>
                </p>
                <p style='text-align: center; margin-bottom: 60px;'>
                    <a href='{$declineLink}' style='text-decoration: none; color: white; background-color: #f44336; padding: 10px 20px; border-radius: 5px;'>Decline Invitation</a>
                </p>
                <p style='text-align: center; margin-bottom: 60px;'>
                    <a href='{$dontKnowLink}' style='text-decoration: none; color: white; background-color: #FF9800; padding: 10px 20px; border-radius: 5px;'>Not Sure Yet</a>
                </p>
                {$wishlistHtml}
                <p style='margin-top: 20px;'>Looking forward to seeing you!</p>
                <p>Best Regards,</p>
                <p>The MammaMiaMarcello Team</p>
            </body>
            </html>";

        // Plain text version of the email (alternative body)
        $mail->AltBody = "
            You are invited to a special event!\n\n
            {$eventName}\n
            {$eventDescription}\n
            " . ($externalLink ? "Visit the event link: {$externalLink}\n" : "") . "
            To RSVP, please click one of the options below:\n
            Accept Invitation: {$acceptLink}\n
            Decline Invitation: {$declineLink}\n
            Not Sure Yet: {$dontKnowLink}\n
            {$wishlistHtml}\n
            Looking forward to seeing you!\n
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