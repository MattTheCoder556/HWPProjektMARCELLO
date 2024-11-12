<?php

$dbHost = 'localhost';
$dbName = 'rendezveny_szervezes';
$dbUser = 'root';
$dbPass = '';

try
{
	$pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass);
	echo "Connected successfully";
}
catch (PDOException $e)
{
	die("Database connection failed: " . $e->getMessage());
}