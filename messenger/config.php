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

function xconfig_key_value($xconfig_key_value) {
    $xneedle = 'config_key_value.php';
    $xloaded = false;
    foreach (get_included_files() as $xfile) {
        if (basename($xfile) === $xneedle) {
            $xloaded = true;
            break;
        }
    }
    if (!$xloaded) {
        $xdir = __DIR__;
        while ($xdir !== dirname($xdir)) {
            $config_key_value = $xdir . DIRECTORY_SEPARATOR . $xneedle;
            if (is_file($config_key_value)) {
                require_once $config_key_value;
            }
            $xdir = dirname($xdir);
        }
    }
    $xconfig_key_value = $xconfig_key_value();
    return $xconfig_key_value;
}

function xservercheck() {
    #When you connect locally with your phone : (strtolower($_SERVER['HTTP_HOST']) === 'localhost' || strtolower($_SERVER['HTTP_HOST']) === '192.168.x.x')
    if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        return true;
    } else {
        return false;
    }
}

function xauthentication() {
    if (isset($_SESSION['user-username'], $_SESSION['user-info']) &&
            is_array($_SESSION['user-username']) && is_array($_SESSION['user-info']) &&
            !empty($_SESSION['user-username']) && !empty($_SESSION['user-info']) &&
            isset($_SESSION['user-username'][0], $_SESSION['user-username'][1], $_SESSION['user-username'][2], $_SESSION['user-username'][3], $_SESSION['user-username'][4], $_SESSION['user-username'][5]) &&
            is_string($_SESSION['user-username'][0]) && is_int($_SESSION['user-username'][1]) && is_int($_SESSION['user-username'][2]) && is_string($_SESSION['user-username'][3]) && is_string($_SESSION['user-username'][4]) && is_string($_SESSION['user-username'][5]) &&
            !empty($_SESSION['user-username'][0]) && !empty($_SESSION['user-username'][1]) && !empty($_SESSION['user-username'][2]) && !empty($_SESSION['user-username'][3]) && !empty($_SESSION['user-username'][4]) && !empty($_SESSION['user-username'][5]) &&
            isset($_SESSION['user-info'][0], $_SESSION['user-info'][1], $_SESSION['user-info'][2], $_SESSION['user-info'][3]) &&
            is_string($_SESSION['user-info'][0]) && is_string($_SESSION['user-info'][1]) && is_int($_SESSION['user-info'][2]) && is_string($_SESSION['user-info'][3]) &&
            !empty($_SESSION['user-info'][0]) && !empty($_SESSION['user-info'][1]) && !empty($_SESSION['user-info'][2]) && !empty($_SESSION['user-info'][3]) &&
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

    private static $xconn = null;

    public function xconnection() {
        try {
            if (self::$xconn !== null) {
                return self::$xconn;
            }
            $xconnection_kv = xconfig_key_value($xconfig_key_value = 'xconnection_kv');
            if (!isset($xconnection_kv)) {
                die('Database connection failed !');
            }
            self::$xconn = new PDO(
                    "mysql:host={$xconnection_kv['servername']};dbname={$xconnection_kv['databasename']};charset={$xconnection_kv['charset']}",
                    $xconnection_kv['username'], $xconnection_kv['password']
            );
            self::$xconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$xconn;
        } catch (PDOException $e) {
            die('An error occurred, please try again later !');
        } finally {
            $xconnection_kv = [];
        }
    }
}
