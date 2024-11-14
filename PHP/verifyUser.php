<?php
require_once("config.php");
require_once("functions.php");

if($_SERVER["REQUEST_METHOD"] == "GET")
{
	$token = $_GET["token"];
	try
	{
		$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $pdo->prepare("SELECT username FROM registration_tokens WHERE reg_token = :token");
		$stmt->bindParam(":token", $token);
		$stmt->execute();

		// Check if any token was found
		if ($stmt->rowCount() == 0)
		{
			redirectToLogin("Invalid or expired verification token.", null);
			exit();
		}

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$username = $result['username'];


		// Update the user's verification status
		$stmt2 = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE username = :username");
		$stmt2->bindParam(":username", $username);
		$stmt2->execute();

		// Delete the used token from registration_tokens to make it single-use
		$stmt3 = $pdo->prepare("DELETE FROM registration_tokens WHERE reg_token = :token");
		$stmt3->bindParam(":token", $token);
		$stmt3->execute();

		if($stmt->rowCount() > 0)
		{
			redirectToLogin("Your account is now verified, you can log in!", null);
		}
	}
	catch (PDOException $e)
	{
		echo "Database error: " . htmlspecialchars($e->getMessage());
	}
	finally
	{
		$pdo = null;
	}
}
else
{
	redirectToLogin("Invalid request", null);
}
