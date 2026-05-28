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

#Rate Limiting : HTTP 429 (Too Many Requests)
if (isset($_SESSION['user-username'])) {
    if ((time() - $_SESSION['user-username'][2]) <= 4) {
        $_SESSION['user-username'][1]++;
    } else {
        $_SESSION['user-username'][1] = 1;
        $_SESSION['user-username'][2] = time();
    }
}

function xgeneratecsrftoken() {
    if (!isset($_SESSION['xtokenlogin'])) {
        $_SESSION['xtokenlogin'] = array(bin2hex(random_bytes(64)), 1);
    }
}

function xauthentication() {
    if (isset($_SESSION['user-username'], $_SESSION['user-info']) &&
            is_array($_SESSION['user-username']) && is_array($_SESSION['user-info']) &&
            !empty($_SESSION['user-username']) && !empty($_SESSION['user-info']) &&
            #
            isset($_SESSION['user-username'][0], $_SESSION['user-username'][1], $_SESSION['user-username'][2], $_SESSION['user-username'][3], $_SESSION['user-username'][4], $_SESSION['user-username'][5]) &&
            is_string($_SESSION['user-username'][0]) && is_int($_SESSION['user-username'][1]) && is_int($_SESSION['user-username'][2]) && is_string($_SESSION['user-username'][3]) && is_string($_SESSION['user-username'][4]) && is_string($_SESSION['user-username'][5]) &&
            !empty($_SESSION['user-username'][0]) && !empty($_SESSION['user-username'][1]) && !empty($_SESSION['user-username'][2]) && !empty($_SESSION['user-username'][3]) && !empty($_SESSION['user-username'][4]) && !empty($_SESSION['user-username'][5]) &&
            #
            isset($_SESSION['user-info'][0], $_SESSION['user-info'][1], $_SESSION['user-info'][2], $_SESSION['user-info'][3]) &&
            is_string($_SESSION['user-info'][0]) && is_string($_SESSION['user-info'][1]) && is_int($_SESSION['user-info'][2]) && is_string($_SESSION['user-info'][3]) &&
            !empty($_SESSION['user-info'][0]) && !empty($_SESSION['user-info'][1]) && !empty($_SESSION['user-info'][2]) && !empty($_SESSION['user-info'][3]) &&
            #
            hash_equals($_SESSION['user-username'][3], $_SESSION['user-info'][3]) &&
            hash_equals($_SESSION['user-username'][4], session_id()) &&
            ($_SESSION['user-info'][1] === $_SERVER['HTTP_USER_AGENT']) &&
            ($_SESSION['user-info'][0] === $_SERVER['REMOTE_ADDR']) &&
            ((time() - $_SESSION['user-info'][2]) <= 86400)) {
        return true;
    } else {
        return false;
    }
}

class Connection {

    public $key1 = "", $key2 = "";

    private function xloadEnv() {
        #Keep it (.env) outside the public directory 😂
        foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $xline) {
            putenv($xline);
        }
    }

    public function __construct() {

        $this->xloadEnv();
        $this->key1 = getenv('key1');
        $this->key2 = getenv('key2');
    }

    public function xconnection() {

        try {

            $xconnection = new PDO("mysql:host=" . getenv('servername') . ";dbname=" . getenv('databasename') . ";charset=" . getenv('charset') . ";", getenv('username'), getenv('password'));
            $xconnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $xconnection;
        } catch (PDOException $e) {

            die("An error occurred, please try again later !");
        } finally {

            unset($xconnection);
        }
    }
}

?>
