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

function xgeneratecsrftoken() {
    if (!isset($_SESSION['xtokenlogin'])) {
        $_SESSION['xtokenlogin'] = array(bin2hex(random_bytes(64)), 1);
    }
}

function xauthentication() {
    return (isset($_SESSION['user-username'], $_SESSION['user-info']) &&
            is_array($_SESSION['user-username']) && is_array($_SESSION['user-info']) &&
            !empty($_SESSION['user-username']) && !empty($_SESSION['user-info']) &&
            isset($_SESSION['user-username'][0], $_SESSION['user-username'][1], $_SESSION['user-username'][2], $_SESSION['user-username'][3], $_SESSION['user-username'][4]) &&
            is_string($_SESSION['user-username'][0]) && is_int($_SESSION['user-username'][1]) && is_int($_SESSION['user-username'][2]) && is_string($_SESSION['user-username'][3]) && is_string($_SESSION['user-username'][4]) &&
            !empty($_SESSION['user-username'][0]) && !empty($_SESSION['user-username'][1]) && !empty($_SESSION['user-username'][2]) && !empty($_SESSION['user-username'][3]) && !empty($_SESSION['user-username'][4]) &&
            isset($_SESSION['user-info'][0], $_SESSION['user-info'][1], $_SESSION['user-info'][2], $_SESSION['user-info'][3]) &&
            is_string($_SESSION['user-info'][0]) && is_string($_SESSION['user-info'][1]) && is_int($_SESSION['user-info'][2]) && is_string($_SESSION['user-info'][3]) &&
            !empty($_SESSION['user-info'][0]) && !empty($_SESSION['user-info'][1]) && !empty($_SESSION['user-info'][2]) && !empty($_SESSION['user-info'][3]) &&
            hash_equals($_SESSION['user-username'][3], $_SESSION['user-info'][3]) &&
            hash_equals($_SESSION['user-username'][4], session_id()) &&
            ($_SESSION['user-info'][1] === $_SERVER['HTTP_USER_AGENT']) &&
            ($_SESSION['user-info'][0] === $_SERVER['REMOTE_ADDR']) &&
            ((time() - $_SESSION['user-info'][2]) <= 86400));
}

function xloadEnv() {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $xline) {
        putenv(trim($xline));
    }
}

xloadEnv(__DIR__ . '/.env'); #Keep it outside the public directory 😂

$key1 = getenv('key1');
$key2 = getenv('key2');

$servername = getenv('servername');
$databasename = getenv('databasename');
$username = getenv('username');
$password = getenv('password');
$charset = getenv('charset');
try {
    $xconnection = new PDO("mysql:host=$servername;dbname=$databasename;charset=$charset;", $username, $password);
    $xconnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Unfortunately, there is a problem !");
}
?>
