<?php

$dbHost = 'localhost';
$dbName = 'mmm';
$dbUser = 'mmm';
$dbPass = 'R7fBWxF4bx1w1lq';

try
{
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    die("Database connection failed: " . $e->getMessage());
}

$apiBaseUrl = 'http://localhost/HWPProjectMarcello/PHP/api';
//$apiBaseUrl = 'http://localhost/HWP_2024/HWPProjektMARCELLO/PHP/api';