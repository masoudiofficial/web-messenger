<?php

require_once '../config.php';

class Isud {

    private $xconnection = null;

    private function xdatetime() {

        $now = new DateTime("now", new DateTimeZone("America/New_York"));
        $xdatetime = $now->format("Y-m-d H:i:s");
        return $xdatetime;
    }

    private function xremovedirectory($path) {
        if (!is_dir($path))
            return false;
        $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($items as $item) {
            if ($item->isDir()) {
                if (!rmdir($item->getPathname()))
                    return false;
            } else {
                if (!unlink($item->getPathname()))
                    return false;
            }
        }
        return rmdir($path);
    }

    public function __construct() {

        $xconnection = new Connection();
        $this->xconnection = $xconnection->xconnection();
    }

    public function xaddperson() {

        try {

            $xselect1 = $this->xconnection->prepare("SELECT username FROM userstable WHERE username=?");
            $xselect1->execute([$_POST["xaddpersonusername"]]);
            $xselect1_r = $xselect1->fetch(PDO::FETCH_ASSOC);

            if (empty($xselect1_r)) {

                $this->xconnection->beginTransaction();

                $xinsert1 = $this->xconnection->prepare("INSERT INTO userstable (username, username2, password, creationdate, messages, status, blocks) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $xinsert1->execute([$_POST["xaddpersonusername"], str_shuffle('qwertyuiopasdfghjklzxcvbnm0123456789QWERTYUIOPASDFGHJKLZXCVBNM'), password_hash($_POST["xaddpersonpassword"], PASSWORD_DEFAULT), $this->xdatetime(), "", $this->xdatetime(), ""]);
                # ...

                if ($xinsert1->rowCount() > 0) {

                    $this->xconnection->commit();

                    $xfolder = $_POST["xaddpersonusername"];
                    $xsubfolders = ['images', 'audios', 'videos'];
                    if (!is_dir("../$xfolder") && mkdir("../$xfolder", 0755, true) && copy("../assets/accountimage.png", "../$xfolder/accountimage.png")) {
                        foreach ($xsubfolders as $xsf) {
                            mkdir("../$xfolder" . DIRECTORY_SEPARATOR . $xsf, 0755, true);
                        }
                        echo json_encode(array("xmessage" => "User added !", "xtype" => "#239f40"));
                    } else {
                        echo json_encode(array("xmessage" => "Unfortunately, there is a problem !", "xtype" => "#ffa500"));
                    }
                } else {

                    if ($this->xconnection->inTransaction()) {
                        $this->xconnection->rollBack();
                    }
                    echo json_encode(array("xmessage" => "Unfortunately, there is a problem !", "xtype" => "#ffa500"));
                }
            } else {
                echo json_encode(array("xmessage" => "This username is already taken !", "xtype" => "#ffa500"));
            }
        } catch (Throwable $e) {
            echo json_encode(array("xmessage" => "Unfortunately, there is a problem !", "xtype" => "#ffa500"));
        } finally {
            $this->xconnection = null;
        }
    }

    public function xloginbutton() {

        $_POST['xtokenlogin'] = htmlspecialchars(strip_tags($_POST['xtokenlogin']));
        if (!isset($_SESSION['xtokenlogin']) || !is_array($_SESSION['xtokenlogin']) || empty($_SESSION['xtokenlogin']) || !isset($_SESSION['xtokenlogin'][0], $_SESSION['xtokenlogin'][1], $_POST['xtokenlogin']) || !is_string($_SESSION['xtokenlogin'][0]) || !is_int($_SESSION['xtokenlogin'][1]) || !is_string($_POST['xtokenlogin']) || empty($_SESSION['xtokenlogin'][0]) || empty($_SESSION['xtokenlogin'][1]) || empty($_POST['xtokenlogin']) || !hash_equals($_SESSION['xtokenlogin'][0], $_POST['xtokenlogin']) || $_SESSION['xtokenlogin'][1] >= 3) {
            echo json_encode(array("xmessage" => "Not possible !", "xtype" => "#ffa500"));
        } else {

            if ($_SESSION['xtokenlogin'][1] >= 3) {
                $_SESSION['xtokenlogin'] = [bin2hex(random_bytes(64)), ++$_SESSION['xtokenlogin'][1]];
            } else {
                $_SESSION['xtokenlogin'][1]++;
            }

            try {

                $xselect1 = $this->xconnection->prepare("SELECT username2, password FROM userstable WHERE username=?");
                $xselect1->execute([$_POST["xloginusername"]]);
                $xselect1_r = $xselect1->fetch(PDO::FETCH_ASSOC);

                if (!empty($xselect1_r) && password_verify($_POST['xloginpassword'], $xselect1_r['password'])) {

                    session_regenerate_id(true);
                    $xsecuritytoken = bin2hex(random_bytes(64));
                    $_SESSION['user-username'] = array($_POST['xloginusername'], 1, time(), $xsecuritytoken, session_id(), $xselect1_r['username2']);
                    $_SESSION['user-info'] = array($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], time(), $xsecuritytoken);

                    echo json_encode(array("xmessage" => "Login !", "xtype" => "#239f40"));
                } else {
                    echo json_encode(array("xmessage" => "Username or password is invalid !", "xtype" => "#ffa500"));
                }
            } catch (Throwable $e) {
                echo json_encode(array("xmessage" => "Unfortunately, there is a problem !", "xtype" => "#ffa500"));
            } finally {
                $this->xconnection = null;
            }
        }
    }

    public function xdeleteperson() {

        try {

            $xselect1 = $this->xconnection->prepare("SELECT password FROM userstable WHERE username=?");
            $xselect1->execute([$_POST["xdeletepersonusername"]]);
            $xselect1_r = $xselect1->fetch(PDO::FETCH_ASSOC);

            if (!empty($xselect1_r) && password_verify($_POST['xdeletepersonpassword'], $xselect1_r['password'])) {

                $this->xconnection->beginTransaction();

                $xdelete1 = $this->xconnection->prepare("DELETE FROM userstable WHERE username=?");
                $xdelete1->execute([$_POST["xdeletepersonusername"]]);
                # ...

                if ($xdelete1->rowCount() > 0) {

                    $this->xconnection->commit();

                    if ($this->xremovedirectory('../' . $_POST["xdeletepersonusername"])) {
                        echo json_encode(array("xmessage" => "User deleted !", "xtype" => "#239f40"));
                    } else {
                        echo json_encode(array("xmessage" => "Unfortunately, there is a problem !", "xtype" => "#ffa500"));
                    }
                } else {

                    if ($this->xconnection->inTransaction()) {
                        $this->xconnection->rollBack();
                    }
                    echo json_encode(array("xmessage" => "Unfortunately, there is a problem !", "xtype" => "#ffa500"));
                }
            } else {
                echo json_encode(array("xmessage" => "Username or password is invalid !", "xtype" => "#ffa500"));
            }
        } catch (Throwable $e) {
            echo json_encode(array("xmessage" => "Unfortunately, there is a problem !", "xtype" => "#ffa500"));
        } finally {
            $this->xconnection = null;
        }
    }
}

if (isset($_POST["xaddperson"]) && !empty($_POST["xaddperson"]) && preg_match("/^[a-z]+$/", $_POST["xaddperson"]) && $_POST["xaddperson"] === "xtrue" && empty($_FILES)) {
    #if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' && strtolower($_SERVER['HTTP_HOST']) === 'example.com' && $_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['xrequestreferrer'] === 'index.php') {
    if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER["REQUEST_METHOD"] === 'POST' && $_SESSION['xrequestreferrer'] === 'index.php') {#When you connect locally with your phone : (strtolower($_SERVER['HTTP_HOST']) === 'localhost' || strtolower($_SERVER['HTTP_HOST']) === '192.168.x.x')
        $xnotallowed = ['assets', 'modules', 'src'];
        if (isset($_POST["xaddpersonusername"]) && !empty($_POST["xaddpersonusername"]) && preg_match("/^[a-z0-9]+$/", $_POST["xaddpersonusername"]) && strlen($_POST["xaddpersonusername"]) <= 32 && !in_array($_POST['xaddpersonusername'], $xnotallowed, true)) {

            $xisud = new Isud();
            $xisud->xaddperson();
            unset($xisud);
        } else {
            echo json_encode(array("xmessage" => "32 allowed characters : 0-9 a-z", "xtype" => "#ffa500"));
        }
    } else {
        echo json_encode(array("xmessage" => "Not possible !", "xtype" => "#ffa500"));
    }
}

if (isset($_POST['xloginbutton']) && !empty($_POST['xloginbutton']) && preg_match('/^[a-z]+$/', $_POST['xloginbutton']) && $_POST['xloginbutton'] === "xtrue" && empty($_FILES)) {
    if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['xrequestreferrer'] === 'index.php') {
        if (isset($_POST['xloginusername']) && !empty($_POST['xloginusername']) && preg_match('/^[a-z0-9]+$/', $_POST['xloginusername']) && strlen($_POST["xloginusername"]) <= 32) {

            $xisud = new Isud();
            $xisud->xloginbutton();
            unset($xisud);
        } else {
            echo json_encode(array("xmessage" => "32 allowed characters : 0-9 a-z", "xtype" => "#ffa500"));
        }
    } else {
        echo json_encode(array("xmessage" => "Not possible !", "xtype" => "#ffa500"));
    }
}

if (isset($_POST["xdeleteperson"]) && !empty($_POST["xdeleteperson"]) && preg_match("/^[a-z]+$/", $_POST["xdeleteperson"]) && $_POST["xdeleteperson"] === "xtrue" && empty($_FILES)) {
    if (strtolower($_SERVER["HTTP_HOST"]) === "localhost" && $_SERVER["REQUEST_METHOD"] === 'POST' && $_SESSION['xrequestreferrer'] === 'index.php') {
        $xnotallowed = ['assets', 'modules', 'src'];
        if (isset($_POST["xdeletepersonusername"]) && !empty($_POST["xdeletepersonusername"]) && preg_match("/^[a-z0-9]+$/", $_POST["xdeletepersonusername"]) && strlen($_POST["xdeletepersonusername"]) <= 32 && !in_array($_POST['xdeletepersonusername'], $xnotallowed, true)) {

            $xisud = new Isud();
            $xisud->xdeleteperson();
            unset($xisud);
        } else {
            echo json_encode(array("xmessage" => "32 allowed characters : 0-9 a-z", "xtype" => "#ffa500"));
        }
    } else {
        echo json_encode(array("xmessage" => "Not possible !", "xtype" => "#ffa500"));
    }
}
?>
