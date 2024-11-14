<?php
require "config.php";
require "functions.php";

$username = $_POST['username'];
$password = $_POST['password'];

if (empty($username) || empty($password))
{
	redirectToLogin("Both fields are required to login.", $_POST);
}

loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass);