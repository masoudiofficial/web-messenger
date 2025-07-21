<?php

session_set_cookie_params([
    'path' => '/',
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]); #When you connect locally with your phone too : 'secure' => false
session_start();
header("Access-Control-Allow-Origin: localhost");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
$xnonce = base64_encode(hash('sha512', str_shuffle('g1o9vVT)D$2Pkzba4hG7u&rLF5HMfe@Ni^sU%WBQI(dYt6nA#X8c0ERmKCwx*SlOpqjZJ!3y') . random_bytes(64), true));
header("Content-Security-Policy: script-src 'nonce-$xnonce'; style-src 'self' 'nonce-$xnonce' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com");

$servername = "localhost";
$databasename = "messenger";
$username = "root";
$password = "";
$charset = "utf8mb4";
try {
    $xconnection = new PDO("mysql:host=$servername;dbname=$databasename;charset=$charset;", $username, $password);
    $xconnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("An error occurred, please try again later !");
}
?>
