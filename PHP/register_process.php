<?php
require "config.php";
require "functions.php";

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$username = $_POST['username'];
$phone = $_POST['phone'];
$password = $_POST['password'];

if (empty($firstname) || empty($lastname) || empty($username) || empty($phone) || empty($password))
{
	redirectToRegister("All fields are required.", $_POST);
}

registerUser($firstname, $lastname, $username, $phone, $password, $dbHost, $dbName, $dbUser, $dbPass);
