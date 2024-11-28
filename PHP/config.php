<?php

$dbHost = 'localhost';
$dbName = 'marcello_test';
$dbUser = 'root';
$dbPass = '';

try
{
	$pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass);
}
catch (PDOException $e)
{
	die("Database connection failed: " . $e->getMessage());
}