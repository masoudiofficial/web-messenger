<?php

include_once './config.php';

function xdatetime() {
    $now = new DateTime("now", new DateTimeZone("America/New_York"));
    $xdatetime = $now->format("Y-m-d H:i:s");
    return $xdatetime;
}

if (isset($_POST["xaddperson"]) && !empty($_POST["xaddperson"]) && preg_match("/^[a-z]+$/", $_POST["xaddperson"]) && $_POST["xaddperson"] === "xtrue") {
    #if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' && strtolower($_SERVER['HTTP_HOST']) === 'example.com' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER["REQUEST_METHOD"] === "POST") {#When you connect locally with your phone : (strtolower($_SERVER['HTTP_HOST']) === 'localhost' || strtolower($_SERVER['HTTP_HOST']) === '192.168.x.x')
        if (preg_match("/^[a-z0-9]+$/", $_POST["xaddpersonusername"]) && strlen($_POST["xaddpersonusername"]) <= 32) {

            $xselectusername = $xconnection->prepare("SELECT username FROM userstable WHERE username=?");
            $xselectusername->execute([$_POST["xaddpersonusername"]]);
            $xselectusername = $xselectusername->fetch(PDO::FETCH_ASSOC);

            if ($xselectusername === false) {

                $xinsertperson = $xconnection->prepare("INSERT INTO userstable (username, password, creationdate, messages, status, blocks) VALUES (?, ?, ?, ?, ?, ?)");
                $xinsertperson->execute([$_POST["xaddpersonusername"], password_hash($_POST["xaddpersonpassword"], PASSWORD_DEFAULT), xdatetime(), "", "1000-01-01 00:00:00", ""]);

                $xparent = $_POST["xaddpersonusername"];
                $xsubfolders = ['images', 'audios', 'videos'];
                if (!is_dir($xparent)) {
                    mkdir($xparent, 0755, true);
                    copy("./imageaccount.png", "./" . $xparent . "/imageaccount.png");
                }
                foreach ($xsubfolders as $xfolder) {
                    $xpath = $xparent . DIRECTORY_SEPARATOR . $xfolder;
                    if (!is_dir($xpath)) {
                        mkdir($xpath, 0755, true);
                    }
                }

                $xresponse = array("xmessage" => "Username added !", "xtype" => "#239f40");
                echo json_encode($xresponse);
            } else {
                $xresponse = array("xmessage" => "This username is already taken !", "xtype" => "#ffa500");
                echo json_encode($xresponse);
            }
            $xconnection = null;
        } else {
            $xresponse = array("xmessage" => "Maximum number of characters : 32 & allowed characters : 0-9 a-z", "xtype" => "#ffa500");
            echo json_encode($xresponse);
        }
    } else {
        $xresponse = array("xmessage" => "Not possible !", "xtype" => "#ffa500");
        echo json_encode($xresponse);
    }
}

if (isset($_POST['xloginbutton']) && !empty($_POST['xloginbutton']) && preg_match('/^[a-z]+$/', $_POST['xloginbutton']) && $_POST['xloginbutton'] === 'xtrue') {
    if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['xloginusername']) && preg_match('/^[a-z0-9]+$/', $_POST['xloginusername']) && strlen($_POST["xloginusername"]) <= 32) {

            if (!isset($_POST['xtokenlogin']) || empty(htmlspecialchars(strip_tags($_POST['xtokenlogin']), ENT_QUOTES, 'UTF-8')) || !isset($_SESSION['xtokenlogin']) || empty(htmlspecialchars(strip_tags($_SESSION['xtokenlogin'][0]), ENT_QUOTES, 'UTF-8')) || !hash_equals(htmlspecialchars(strip_tags($_POST['xtokenlogin']), ENT_QUOTES, 'UTF-8'), htmlspecialchars(strip_tags($_SESSION['xtokenlogin'][0]), ENT_QUOTES, 'UTF-8')) || empty(htmlspecialchars(strip_tags($_SESSION['xtokenlogin'][1]), ENT_QUOTES, 'UTF-8')) || htmlspecialchars(strip_tags($_SESSION['xtokenlogin'][1]), ENT_QUOTES, 'UTF-8') >= '3') {
                $xresponse = array("xmessage" => "Not possible !", "xtype" => "#ffa500");
                echo json_encode($xresponse);
            } else {

                $_SESSION['xtokenlogin'][1]++;
                if (htmlspecialchars(strip_tags($_SESSION['xtokenlogin'][1]), ENT_QUOTES, 'UTF-8') >= '3') {
                    $_SESSION['xtokenlogin'][0] = bin2hex(random_bytes(64));
                }

                $xselectusername = $xconnection->prepare("SELECT password FROM userstable WHERE username=?");
                $xselectusername->execute([$_POST['xloginusername']]);
                $xselectusername = $xselectusername->fetch(PDO::FETCH_ASSOC);

                if ($xselectusername !== false && password_verify($_POST['xloginpassword'], $xselectusername['password'])) {

                    session_regenerate_id(true);
                    $xsecuritytoken = str_shuffle('g1o9vVT)D$2Pkzba4hG7u&rLF5HMfe@Ni^sU%WBQI(dYt6nA#X8c0ERmKCwx*SlOpqjZJ!3y');
                    $_SESSION['user-username'] = array($_POST['xloginusername'], 0, time(), $xsecuritytoken);
                    $_SESSION['user-info'] = array($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], time(), $xsecuritytoken);

                    $xupdatepassword = $xconnection->prepare("UPDATE userstable SET password=? WHERE username=?");
                    $xupdatepassword->execute([password_hash($_POST['xloginpassword'], PASSWORD_DEFAULT), $_POST['xloginusername']]);
                } else {
                    $xresponse = array("xmessage" => "Username or password is invalid !", "xtype" => "#ffa500");
                    echo json_encode($xresponse);
                }
                $xconnection = null;
            }
        } else {
            $xresponse = array("xmessage" => "Maximum number of characters : 32 & allowed characters : 0-9 a-z", "xtype" => "#ffa500");
            echo json_encode($xresponse);
        }
    } else {
        $xresponse = array("xmessage" => "Not possible !", "xtype" => "#ffa500");
        echo json_encode($xresponse);
    }
}

if (isset($_POST["xdeleteperson"]) && !empty($_POST["xdeleteperson"]) && preg_match("/^[a-z]+$/", $_POST["xdeleteperson"]) && $_POST["xdeleteperson"] === "xtrue") {
    if (strtolower($_SERVER["HTTP_HOST"]) === "localhost" && $_SERVER["REQUEST_METHOD"] === "POST") {
        if (preg_match("/^[a-z0-9]+$/", $_POST["xdeletepersonusername"]) && strlen($_POST["xdeletepersonusername"]) <= 32) {

            $xselectusername = $xconnection->prepare("SELECT password FROM userstable WHERE username=?");
            $xselectusername->execute([$_POST['xdeletepersonusername']]);
            $xselectusername = $xselectusername->fetch(PDO::FETCH_ASSOC);

            if ($xselectusername !== false && password_verify($_POST['xdeletepersonpassword'], $xselectusername['password'])) {

                $xdeleteusername = $xconnection->prepare("DELETE FROM userstable WHERE username=?");
                $xdeleteusername->execute([$_POST["xdeletepersonusername"]]);

                function xrecursedelete($xpathdelete) {
                    $xfiles = array_diff(scandir($xpathdelete), array('.', '..'));
                    foreach ($xfiles as $xfile) {
                        if (is_dir("$xpathdelete/$xfile")) {
                            xrecursedelete("$xpathdelete/$xfile");
                        } else {
                            unlink("$xpathdelete/$xfile");
                        }
                    }
                    rmdir($xpathdelete);
                }

                xrecursedelete('./' . $_POST["xdeletepersonusername"]);

                $xresponse = array("xmessage" => "Username deleted !", "xtype" => "#239f40");
                echo json_encode($xresponse);
            } else {
                $xresponse = array("xmessage" => "Username or password is invalid !", "xtype" => "#ffa500");
                echo json_encode($xresponse);
            }
            $xconnection = null;
        } else {
            $xresponse = array("xmessage" => "Maximum number of characters : 32 & allowed characters : 0-9 a-z", "xtype" => "#ffa500");
            echo json_encode($xresponse);
        }
    } else {
        $xresponse = array("xmessage" => "Not possible !", "xtype" => "#ffa500");
        echo json_encode($xresponse);
    }
}

if (isset($_SESSION['user-username'], $_SESSION['user-info']) && (htmlspecialchars(strip_tags($_SESSION['user-info'][0]), ENT_QUOTES, 'UTF-8') === $_SERVER['REMOTE_ADDR']) && (htmlspecialchars(strip_tags($_SESSION['user-info'][1]), ENT_QUOTES, 'UTF-8') === $_SERVER['HTTP_USER_AGENT']) && (time() - htmlspecialchars(strip_tags($_SESSION['user-info'][2]), ENT_QUOTES, 'UTF-8') <= 86400) && (htmlspecialchars(strip_tags($_SESSION['user-username'][3]), ENT_QUOTES, 'UTF-8') === htmlspecialchars(strip_tags($_SESSION['user-info'][3]), ENT_QUOTES, 'UTF-8'))) {

    if (isset($_POST['xuploadimage']) && !empty($_POST["xuploadimage"]) && preg_match("/^[a-z]+$/", $_POST["xuploadimage"]) && $_POST["xuploadimage"] === "xtrue") {
        if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_FILES['ximagetoupload']['name']) && $_FILES['ximagetoupload']['size'] !== 0) {
                $xtargetimagetype = pathinfo($_FILES['ximagetoupload']['name'], PATHINFO_EXTENSION);
                if ($xtargetimagetype === 'gif' or $xtargetimagetype === 'heic' or $xtargetimagetype === 'heif' or $xtargetimagetype === 'jpeg' or $xtargetimagetype === 'jpg' or $xtargetimagetype === 'png') {
                    if (file_exists('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/imageaccount.png')) {
                        unlink('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/imageaccount.png');
                    }
                    if (move_uploaded_file($_FILES['ximagetoupload']['tmp_name'], './' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/imageaccount.png')) {
                        $xresponse = array("xmessage" => './' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/imageaccount.png?t=' . filemtime('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/imageaccount.png'), "xtype" => "#239f40");
                        echo json_encode($xresponse);
                    } else {
                        $xresponse = array("xmessage" => "Not done !", "xtype" => "#ffa500");
                        echo json_encode($xresponse);
                    }
                } else {
                    $xresponse = array("xmessage" => "Invalid format !", "xtype" => "#ffa500");
                    echo json_encode($xresponse);
                }
            } else {
                $xresponse = array("xmessage" => "Just choose one image !", "xtype" => "#ffa500");
                echo json_encode($xresponse);
            }
        } else {
            $xresponse = array("xmessage" => "Not possible !", "xtype" => "#ffa500");
            echo json_encode($xresponse);
        }
    }

    function xpathsizeformat($xpathsizeformat) {
        $xpathsizeformatunits = explode(' ', 'B KB MB GB TB PB');
        $xpathsizeformatmod = 1024;
        for ($i = 0; $xpathsizeformat > $xpathsizeformatmod; $i++) {
            $xpathsizeformat /= $xpathsizeformatmod;
        }
        $xendindex = strpos($xpathsizeformat, '.') + 3;
        return substr($xpathsizeformat, 0, $xendindex) . " $xpathsizeformatunits[$i]";
    }

    function xpathsizeg($xpath, $xextensions = []) {
        $xtotalsize = 0;
        $xfiles = glob("$xpath/*");
        foreach ($xfiles as $xfile) {
            if (strpos(strtolower($xfile), './imageaccount.png') === false) {
                if (is_file($xfile) && (empty($xextensions) || in_array(pathinfo($xfile, PATHINFO_EXTENSION), $xextensions))) {
                    $xtotalsize += filesize($xfile);
                } else if (is_dir($xfile)) {
                    $xtotalsize += xpathsizeg($xfile, $xextensions);
                }
            }
        }
        return $xtotalsize;
    }

    if (isset($_POST['xcirclegraph']) && !empty($_POST['xcirclegraph']) && preg_match('/^[a-z]+$/', $_POST['xcirclegraph']) && $_POST['xcirclegraph'] === 'xtrue') {
        if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $xtotalspace = 104857600;
            $xaudiosize = xpathsizeg('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8'), ['aac', 'mp3', 'ogg', 'wav']);
            $ximagesize = xpathsizeg('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8'), ['gif', 'heic', 'heif', 'jpeg', 'jpg', 'png']);
            $xvideosize = xpathsizeg('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8'), ['avi', 'hevc', 'mkv', 'mov', 'mp4', 'webm']);
            $xusedspace = $xaudiosize + $ximagesize + $xvideosize;
            $xfreespace = $xtotalspace - $xusedspace;

            $xaudioangle = 360 * ($xaudiosize / $xtotalspace);
            $ximageangle = 360 * ($ximagesize / $xtotalspace);
            $xvideoangle = 360 * ($xvideosize / $xtotalspace);
            $xfreeangle = 360 * ($xfreespace / $xtotalspace);

            $xresponse = array("xaudioangle" => $xaudioangle, "xaudiosize" => xpathsizeformat($xaudiosize), "ximageangle" => $ximageangle, "ximagesize" => xpathsizeformat($ximagesize), "xvideoangle" => $xvideoangle, "xvideosize" => xpathsizeformat($xvideosize), "xfreeangle" => $xfreeangle, "xfreespace" => xpathsizeformat($xfreespace));
            echo json_encode($xresponse);
        }
    }

    function xencryptmessages($xmessages, $creationdate) {
        $xmsg = array_values(array_filter(explode('(p)', $xmessages)));
        if (!empty($xmsg)) {
            for ($i = 0; $i < count($xmsg); $i++) {
                $encryptionKey = 't4fnWN%&hDOLZg98z$lEJG7!C*KjxX)@USH6pcsB3(MQdy1iFI#PukAoaqVver2R^5YTm0bw' . $creationdate;
                $hmacKey = $creationdate . 'ypFZJh!v)om3zs%(YSn60ED4LUjfAN#8C7aXKle5^*&xbd2McIHk1$t@uTBwVPRiGQ9gOrWq';
                $cipher = 'aes-128-gcm';
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
                $tag = '';
                $ciphertext = openssl_encrypt($xmsg[$i], $cipher, $encryptionKey, $options = 0, $iv, $tag);
                $hmac = hash_hmac('sha3-512', $iv . $tag . $ciphertext, $hmacKey, true);
                $xmsg[$i] = base64_encode($iv . $tag . $ciphertext . $hmac);
            }
            return '(p)' . implode('(p)', $xmsg);
        } else {
            return '';
        }
    }

    function xdecryptmessages($xmessages, $creationdate) {
        $xmsg = array_values(array_filter(explode('(p)', $xmessages)));
        if (!empty($xmsg)) {
            for ($i = 0; $i < count($xmsg); $i++) {
                $encryptionKey = 't4fnWN%&hDOLZg98z$lEJG7!C*KjxX)@USH6pcsB3(MQdy1iFI#PukAoaqVver2R^5YTm0bw' . $creationdate;
                $hmacKey = $creationdate . 'ypFZJh!v)om3zs%(YSn60ED4LUjfAN#8C7aXKle5^*&xbd2McIHk1$t@uTBwVPRiGQ9gOrWq';
                $cipher = 'aes-128-gcm';
                $iv_length = openssl_cipher_iv_length($cipher);
                $data = base64_decode($xmsg[$i]);
                $iv = substr($data, 0, $iv_length);
                $tag = substr($data, $iv_length, 16);
                $ciphertext = substr($data, $iv_length + 16, -64);
                $hmac = substr($data, -64);
                $calculatedHmac = hash_hmac('sha3-512', $iv . $tag . $ciphertext, $hmacKey, true);
                if (!hash_equals($hmac, $calculatedHmac)) {
                    return false;
                }
                $xmsg[$i] = openssl_decrypt($ciphertext, $cipher, $encryptionKey, $options = 0, $iv, $tag);
                if ($i === count($xmsg) - 1) {
                    return '(p)' . implode('(p)', $xmsg);
                }
            }
        } else {
            return '';
        }
    }

    function xpathsize($xpathsize) {
        $xtotalpathsize = 0;
        $xpathfoldersfiles = glob("$xpathsize/*");
        foreach ($xpathfoldersfiles as $xpathfolderfile) {
            if (strpos(strtolower($xpathfolderfile), './imageaccount.png') === false) {
                is_file($xpathfolderfile) && $xtotalpathsize += filesize($xpathfolderfile);
                is_dir($xpathfolderfile) && $xtotalpathsize += xpathsize($xpathfolderfile);
            }
        }
        return $xtotalpathsize;
    }

    if (isset($_POST['xsubmitmessages']) && !empty($_POST['xsubmitmessages']) && preg_match('/^[a-z]+$/', $_POST['xsubmitmessages']) && $_POST['xsubmitmessages'] === 'xtrue') {

        if (time() - htmlspecialchars(strip_tags($_SESSION['user-username'][2]), ENT_QUOTES, 'UTF-8') <= '4') {
            $_SESSION['user-username'][1]++;
        } else {
            $_SESSION['user-username'][1] = 1;
            $_SESSION['user-username'][2] = time();
        }

        if (htmlspecialchars(strip_tags($_SESSION['user-username'][1]), ENT_QUOTES, 'UTF-8') <= '8') {

            if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['xreceiversubmitmessage']) && preg_match('/^[a-z0-9]+$/', $_POST['xreceiversubmitmessage'])) {
                    if (!empty($_POST['xmessagesubmitmessage']) || (!empty($_FILES['xuploadfiles']['name'][0]) && $_FILES['xuploadfiles']['size'][0] !== 0)) {
                        if (!empty($_POST['xmessagesubmitmessage']) && preg_match('/^[a-zA-Z0-9!?,. \r\n\p{Emoji_Presentation}]+$/u', $_POST['xmessagesubmitmessage']) && strlen($_POST['xmessagesubmitmessage']) <= 1000 && strpos($_POST['xmessagesubmitmessage'], 'RM') !== false && $_POST['xreceiversubmitmessage'] === htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')) {

                            $xmemessagesselect = $xconnection->prepare("SELECT blocks FROM userstable WHERE username=?");
                            $xmemessagesselect->execute([htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                            $xmemessagesselect = $xmemessagesselect->fetch(PDO::FETCH_ASSOC);

                            $xmeupdateblocks = $xconnection->prepare("UPDATE userstable SET blocks=? WHERE username=?");
                            $xmeupdateblocks->execute([str_replace('(b)' . str_replace('RM', '', $_POST['xmessagesubmitmessage']) . '(bb)', '', $xmemessagesselect['blocks']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                        } else {

                            $xdatetime = xdatetime();

                            $xmessagesubmitmessage = '';
                            #$xunicodeemoji = "\x{231A}\x{231B}\x{23E9}\x{23EA}\x{23EB}\x{23EC}\x{23ED}\x{23EE}\x{23EF}\x{23F0}\x{23F1}\x{23F2}\x{23F3}\x{23F8}\x{23F9}\x{23FA}\x{2614}\x{2615}\x{261D}\x{2648}\x{2649}\x{264A}\x{264B}\x{264C}\x{264D}\x{264E}\x{264F}\x{2650}\x{2651}\x{2652}\x{2653}\x{267F}\x{2693}\x{26A1}\x{26AA}\x{26AB}\x{26BD}\x{26BE}\x{26C4}\x{26C5}\x{26CE}\x{26D4}\x{26EA}\x{26F2}\x{26F3}\x{26F5}\x{26F9}\x{26FA}\x{26FD}\x{2705}\x{270A}\x{270B}\x{270C}\x{270D}\x{2728}\x{274C}\x{274E}\x{2753}\x{2754}\x{2755}\x{2757}\x{2795}\x{2796}\x{2797}\x{27B0}\x{27BF}\x{2B1B}\x{2B1C}\x{2B50}\x{2B55}\x{1F004}\x{1F0CF}\x{1F170}\x{1F171}\x{1F17E}\x{1F17F}\x{1F18E}\x{1F191}\x{1F192}\x{1F193}\x{1F194}\x{1F195}\x{1F196}\x{1F197}\x{1F198}\x{1F199}\x{1F19A}\x{1F201}\x{1F21A}\x{1F22F}\x{1F232}\x{1F233}\x{1F234}\x{1F235}\x{1F236}\x{1F238}\x{1F239}\x{1F23A}\x{1F250}\x{1F251}\x{1F300}\x{1F301}\x{1F302}\x{1F303}\x{1F304}\x{1F305}\x{1F306}\x{1F307}\x{1F308}\x{1F309}\x{1F30A}\x{1F30B}\x{1F30C}\x{1F30D}\x{1F30E}\x{1F30F}\x{1F310}\x{1F311}\x{1F312}\x{1F313}\x{1F314}\x{1F315}\x{1F316}\x{1F317}\x{1F318}\x{1F319}\x{1F31A}\x{1F31B}\x{1F31C}\x{1F31D}\x{231B}\x{1F31E}\x{1F31F}\x{1F320}\x{1F32D}\x{1F32E}\x{1F32F}\x{1F330}\x{1F331}\x{1F332}\x{1F333}\x{1F334}\x{1F335}\x{1F337}\x{1F338}\x{1F339}\x{1F33A}\x{1F33B}\x{1F33C}\x{1F33D}\x{1F33E}\x{1F33F}\x{1F340}\x{1F341}\x{1F342}\x{1F343}\x{1F344}\x{1F345}\x{1F346}\x{1F347}\x{1F348}\x{1F349}\x{1F34A}\x{1F34B}\x{1F34C}\x{1F34D}\x{1F34E}\x{1F34F}\x{1F350}\x{1F351}\x{1F352}\x{1F353}\x{1F354}\x{1F355}\x{1F356}\x{1F357}\x{1F358}\x{1F359}\x{1F35A}\x{1F35B}\x{1F35C}\x{1F35D}\x{1F35E}\x{1F35F}\x{1F360}\x{1F361}\x{1F362}\x{1F363}\x{1F364}\x{1F365}\x{1F366}\x{1F367}\x{1F368}\x{1F369}\x{1F36A}\x{1F36B}\x{1F36C}\x{1F36D}\x{1F36E}\x{1F36F}\x{1F370}\x{1F371}\x{1F372}\x{1F373}\x{1F374}\x{1F375}\x{1F376}\x{1F377}\x{1F378}\x{1F379}\x{1F37A}\x{1F37B}\x{1F37C}\x{1F37E}\x{1F37F}\x{1F380}\x{1F381}\x{1F382}\x{1F383}\x{1F384}\x{1F385}\x{1F386}\x{1F387}\x{1F388}\x{1F389}\x{1F38A}\x{1F38B}\x{1F38C}\x{1F38D}\x{1F38E}\x{1F38F}\x{1F390}\x{1F391}\x{1F392}\x{1F393}\x{1F3A0}\x{1F3A1}\x{1F3A2}\x{1F3A3}\x{1F3A4}\x{1F3A5}\x{1F3A6}\x{1F3A7}\x{1F3A8}\x{1F3A9}\x{1F3AA}\x{1F3AB}\x{1F3AC}\x{1F3AD}\x{1F3AE}\x{1F3AF}\x{1F3B0}\x{1F3B1}\x{1F3B2}\x{1F3B3}\x{1F3B4}\x{1F3B5}\x{1F3B6}\x{1F3B7}\x{1F3B8}\x{1F3B9}\x{1F3BA}\x{1F3BB}\x{1F3BC}\x{1F3BD}\x{1F3BE}\x{1F3BF}\x{1F3C0}\x{1F3C1}\x{1F3C2}\x{1F3C3}\x{1F3C4}\x{1F3C5}\x{1F3C6}\x{1F3C7}\x{1F3C8}\x{1F3C9}\x{1F3CA}\x{1F3CB}\x{1F3CC}\x{1F3CF}\x{1F3D0}\x{1F3D1}\x{1F3D2}\x{1F3D3}\x{1F3E0}\x{1F3E1}\x{1F3E2}\x{1F3E3}\x{1F3E4}\x{1F3E5}\x{1F3E6}\x{1F3E7}\x{1F3E8}\x{1F3E9}\x{1F3EA}\x{1F3EB}\x{1F3EC}\x{1F3ED}\x{1F3EE}\x{1F3EF}\x{1F3F0}\x{1F3F4}\x{1F3F8}\x{1F3F9}\x{1F3FA}\x{1F400}\x{1F401}\x{1F402}\x{1F403}\x{1F404}\x{1F405}\x{1F406}\x{1F407}\x{1F408}\x{1F409}\x{1F40A}\x{1F40B}\x{1F40C}\x{1F40D}\x{1F40E}\x{1F40F}\x{1F410}\x{1F411}\x{1F412}\x{1F413}\x{1F414}\x{1F415}\x{1F416}\x{1F417}\x{1F418}\x{1F419}\x{1F41A}\x{1F41B}\x{1F41C}\x{1F41D}\x{1F41E}\x{1F41F}\x{1F420}\x{1F421}\x{1F422}\x{1F423}\x{1F424}\x{1F425}\x{1F426}\x{1F427}\x{1F428}\x{1F429}\x{1F42A}\x{1F42B}\x{1F42C}\x{1F42D}\x{1F42E}\x{1F42F}\x{1F430}\x{1F431}\x{1F432}\x{1F433}\x{1F434}\x{1F435}\x{1F436}\x{1F437}\x{1F438}\x{1F439}\x{1F43A}\x{1F43B}\x{1F43C}\x{1F43D}\x{1F43E}\x{1F440}\x{1F442}\x{1F443}\x{1F444}\x{1F445}\x{1F446}\x{1F447}\x{1F448}\x{1F449}\x{1F44A}\x{1F44B}\x{1F44C}\x{1F44D}\x{1F44E}\x{1F44F}\x{1F450}\x{1F451}\x{1F452}\x{1F453}\x{1F454}\x{1F455}\x{1F456}\x{1F457}\x{1F458}\x{1F459}\x{1F45A}\x{1F45B}\x{1F45C}\x{1F45D}\x{1F45E}\x{1F45F}\x{1F460}\x{1F461}\x{1F462}\x{1F463}\x{1F464}\x{1F465}\x{1F466}\x{1F467}\x{1F468}\x{1F469}\x{1F46A}\x{1F46B}\x{1F46C}\x{1F46D}\x{1F46E}\x{1F46F}\x{1F470}\x{1F471}\x{1F472}\x{1F473}\x{1F474}\x{1F475}\x{1F476}\x{1F477}\x{1F478}\x{1F479}\x{1F47A}\x{1F47B}\x{1F47C}\x{1F47D}\x{1F47E}\x{1F47F}\x{1F480}\x{1F481}\x{1F482}\x{1F483}\x{1F484}\x{1F485}\x{1F486}\x{1F487}\x{1F488}\x{1F489}\x{1F48A}\x{1F48B}\x{1F48C}\x{1F48D}\x{1F48E}\x{1F48F}\x{1F490}\x{1F491}\x{1F492}\x{1F493}\x{1F494}\x{1F495}\x{1F496}\x{1F497}\x{1F498}\x{1F499}\x{1F49A}\x{1F49B}\x{1F49C}\x{1F49D}\x{1F49E}\x{1F49F}\x{1F4A0}\x{1F4A1}\x{1F4A2}\x{1F4A3}\x{1F4A4}\x{1F4A5}\x{1F4A6}\x{1F4A7}\x{1F4A8}\x{1F4A9}\x{1F4AA}\x{1F4AB}\x{1F4AC}\x{1F4AD}\x{1F4AE}\x{1F4AF}\x{1F4B0}\x{1F4B1}\x{1F4B2}\x{1F4B3}\x{1F4B4}\x{1F4B5}\x{1F4B6}\x{1F4B7}\x{1F4B8}\x{1F4B9}\x{1F4BA}\x{1F4BB}\x{1F4BC}\x{1F4BD}\x{1F4BE}\x{1F4BF}\x{1F4C0}\x{1F4C1}\x{1F4C2}\x{1F4C3}\x{1F4C4}\x{1F4C5}\x{1F4C6}\x{1F4C7}\x{1F4C8}\x{1F4C9}\x{1F4CA}\x{1F4CB}\x{1F4CC}\x{1F4CD}\x{1F4CE}\x{1F4CF}\x{1F4D0}\x{1F4D1}\x{1F4D2}\x{1F4D3}\x{1F4D4}\x{1F4D5}\x{1F4D6}\x{1F4D7}\x{1F4D8}\x{1F4D9}\x{1F4DA}\x{1F4DB}\x{1F4DC}\x{1F4DD}\x{1F4DE}\x{1F4DF}\x{1F4E0}\x{1F4E1}\x{1F4E2}\x{1F4E3}\x{1F4E4}\x{1F4E5}\x{1F4E6}\x{1F4E7}\x{1F4E8}\x{1F4E9}\x{1F4EA}\x{1F4EB}\x{1F4EC}\x{1F4ED}\x{1F4EE}\x{1F4EF}\x{1F4F0}\x{1F4F1}\x{1F4F2}\x{1F4F3}\x{1F4F4}\x{1F4F5}\x{1F4F6}\x{1F4F7}\x{1F4F8}\x{1F4F9}\x{1F4FA}\x{1F4FB}\x{1F4FC}\x{1F4FF}\x{1F500}\x{1F501}\x{1F502}\x{1F503}\x{1F504}\x{1F505}\x{1F506}\x{1F507}\x{1F508}\x{1F509}\x{1F50A}\x{1F50B}\x{1F50C}\x{1F50D}\x{1F50E}\x{1F50F}\x{1F510}\x{1F511}\x{1F512}\x{1F513}\x{1F514}\x{1F515}\x{1F516}\x{1F517}\x{1F518}\x{1F519}\x{1F51A}\x{1F51B}\x{1F51C}\x{1F51D}\x{1F51E}\x{1F51F}\x{1F520}\x{1F521}\x{1F522}\x{1F523}\x{1F524}\x{1F525}\x{1F526}\x{1F527}\x{1F528}\x{1F529}\x{1F52A}\x{1F52B}\x{1F52C}\x{1F52D}\x{1F52E}\x{1F52F}\x{1F530}\x{1F531}\x{1F532}\x{1F533}\x{1F534}\x{1F535}\x{1F536}\x{1F537}\x{1F538}\x{1F539}\x{1F53A}\x{1F53B}\x{1F53C}\x{1F53D}\x{1F54B}\x{1F54C}\x{1F54D}\x{1F54E}\x{1F550}\x{1F551}\x{1F552}\x{1F553}\x{1F554}\x{1F555}\x{1F556}\x{1F557}\x{1F558}\x{1F559}\x{1F55A}\x{1F55B}\x{1F55C}\x{1F55D}\x{1F55E}\x{1F55F}\x{1F560}\x{1F561}\x{1F562}\x{1F563}\x{1F564}\x{1F565}\x{1F566}\x{1F567}\x{1F574}\x{1F575}\x{1F57A}\x{1F590}\x{1F595}\x{1F596}\x{1F5A4}\x{1F5FB}\x{1F5FC}\x{1F5FD}\x{1F5FF}\x{1F600}\x{1F601}\x{1F602}\x{1F603}\x{1F604}\x{1F605}\x{1F606}\x{1F607}\x{1F608}\x{1F609}\x{1F60A}\x{1F60B}\x{1F60C}\x{1F60D}\x{1F60E}\x{1F60F}\x{1F610}\x{1F611}\x{1F612}\x{1F613}\x{1F614}\x{1F615}\x{1F616}\x{1F617}\x{1F618}\x{1F619}\x{1F61A}\x{1F61B}\x{1F61C}\x{1F61D}\x{1F61E}\x{1F61F}\x{1F620}\x{1F621}\x{1F622}\x{1F623}\x{1F624}\x{1F625}\x{1F626}\x{1F627}\x{1F628}\x{1F629}\x{1F62A}\x{1F62B}\x{1F62C}\x{1F62D}\x{1F62E}\x{1F62F}\x{1F630}\x{1F631}\x{1F632}\x{1F633}\x{1F634}\x{1F635}\x{1F636}\x{1F637}\x{1F638}\x{1F639}\x{1F63A}\x{1F63B}\x{1F63C}\x{1F63D}\x{1F63E}\x{1F63F}\x{1F640}\x{1F641}\x{1F642}\x{1F643}\x{1F644}\x{1F645}\x{1F646}\x{1F647}\x{1F648}\x{1F649}\x{1F64A}\x{1F64B}\x{1F64C}\x{1F64D}\x{1F64E}\x{1F64F}\x{1F680}\x{1F681}\x{1F682}\x{1F683}\x{1F684}\x{1F685}\x{1F686}\x{1F687}\x{1F688}\x{1F689}\x{1F68A}\x{1F68B}\x{1F68C}\x{1F68D}\x{1F68E}\x{1F68F}\x{1F690}\x{1F691}\x{1F692}\x{1F693}\x{1F694}\x{1F695}\x{1F696}\x{1F697}\x{1F698}\x{1F699}\x{1F69A}\x{1F69B}\x{1F69C}\x{1F69D}\x{1F69E}\x{1F69F}\x{1F6A0}\x{1F6A1}\x{1F6A2}\x{1F6A3}\x{1F6A4}\x{1F6A5}\x{1F6A6}\x{1F6A7}\x{1F6A8}\x{1F6A9}\x{1F6AA}\x{1F6AB}\x{1F6AC}\x{1F6AD}\x{1F6AE}\x{1F6AF}\x{1F6B0}\x{1F6B1}\x{1F6B2}\x{1F6B3}\x{1F6B4}\x{1F6B5}\x{1F6B6}\x{1F6B7}\x{1F6B8}\x{1F6B9}\x{1F6BA}\x{1F6BB}\x{1F6BC}\x{1F6BD}\x{1F6BE}\x{1F6BF}\x{1F6C0}\x{1F6C1}\x{1F6C2}\x{1F6C3}\x{1F6C4}\x{1F6C5}\x{1F6CC}\x{1F6D0}\x{1F6D1}\x{1F6D2}\x{1F6EB}\x{1F6EC}\x{1F6F4}\x{1F6F5}\x{1F6F6}\x{1F6F7}\x{1F6F8}\x{1F6F9}\x{1F6FA}\x{1F910}\x{1F911}\x{1F912}\x{1F913}\x{1F914}\x{1F915}\x{1F916}\x{1F917}\x{1F918}\x{1F919}\x{1F91A}\x{1F91B}\x{1F91C}\x{1F91D}\x{1F91E}\x{1F91F}\x{1F920}\x{1F921}\x{1F922}\x{1F923}\x{1F924}\x{1F925}\x{1F926}\x{1F927}\x{1F928}\x{1F929}\x{1F92A}\x{1F92B}\x{1F92C}\x{1F92D}\x{1F92E}\x{1F92F}\x{1F930}\x{1F931}\x{1F932}\x{1F933}\x{1F934}\x{1F935}\x{1F936}\x{1F937}\x{1F938}\x{1F939}\x{1F93A}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F940}\x{1F941}\x{1F942}\x{1F943}\x{1F944}\x{1F945}\x{1F947}\x{1F948}\x{1F949}\x{1F94A}\x{1F94B}\x{1F94C}\x{1F94D}\x{1F94E}\x{1F94F}\x{1F950}\x{1F951}\x{1F952}\x{1F953}\x{1F954}\x{1F955}\x{1F956}\x{1F957}\x{1F958}\x{1F959}\x{1F95A}\x{1F95B}\x{1F95C}\x{1F95D}\x{1F95E}\x{1F95F}\x{1F960}\x{1F961}\x{1F962}\x{1F963}\x{1F964}\x{1F965}\x{1F966}\x{1F967}\x{1F968}\x{1F969}\x{1F96A}\x{1F96B}\x{1F980}\x{1F981}\x{1F982}\x{1F983}\x{1F984}\x{1F985}\x{1F986}\x{1F987}\x{1F988}\x{1F989}\x{1F98A}\x{1F98B}\x{1F98C}\x{1F98D}\x{1F98E}\x{1F98F}\x{1F990}\x{1F991}\x{1F992}\x{1F993}\x{1F994}\x{1F995}\x{1F996}\x{1F997}\x{1F9C0}\x{1F9D0}\x{1F9D1}\x{1F9D2}\x{1F9D3}\x{1F9D4}\x{1F9D5}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F9E0}\x{1F9E1}\x{1F9E2}\x{1F9E3}\x{1F9E4}\x{1F9E5}\x{1F9E6}";
                            #if (!empty($_POST['xmessagesubmitmessage']) && preg_match('/^[a-zA-Z0-9!?,. \r\n' . $xunicodeemoji . ']+$/u', $_POST['xmessagesubmitmessage']) && strlen($_POST['xmessagesubmitmessage']) <= 1000) {
                            if (!empty($_POST['xmessagesubmitmessage']) && preg_match('/^[a-zA-Z0-9!?,. \r\n\p{Emoji_Presentation}]+$/u', $_POST['xmessagesubmitmessage']) && strlen($_POST['xmessagesubmitmessage']) <= 1000) {
                                $xmessagesubmitmessage = $_POST['xmessagesubmitmessage'];
                            }

                            $xmefilespath = '';
                            $xreceiverfilespath = '';
                            $xalert = 0;
                            if (!empty($_FILES['xuploadfiles']['name'][0]) && $_FILES['xuploadfiles']['size'][0] !== 0) {
                                if (array_sum(array_filter($_FILES['xuploadfiles']['size'])) < 104857600) {
                                    if (count(array_filter($_FILES['xuploadfiles']['name'])) <= 10) {
                                        $xuploadfilesbytesmax = 0;
                                        foreach ($_FILES['xuploadfiles']['name'] as $xkeys => $xvalues) {
                                            $xuploadfilesbytessize = $_FILES['xuploadfiles']['size'][$xkeys];
                                            $xtargetuploadfiles = '/' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '-' . substr(str_shuffle('kF4dXHVenfSaq5KAyZcuBmUY1PT78pGtLl0sirhCJEMNjwQWDx9zOIR2o3bv6g'), 0, 32) . '.' . pathinfo($_FILES['xuploadfiles']['name'][$xkeys], PATHINFO_EXTENSION);
                                            $xtargetuploadfilestype = pathinfo($xtargetuploadfiles, PATHINFO_EXTENSION);
                                            if ($xtargetuploadfilestype === 'aac' or $xtargetuploadfilestype === 'avi' or $xtargetuploadfilestype === 'gif' or $xtargetuploadfilestype === 'heic' or $xtargetuploadfilestype === 'heif' or $xtargetuploadfilestype === 'hevc' or $xtargetuploadfilestype === 'jpeg' or $xtargetuploadfilestype === 'jpg' or $xtargetuploadfilestype === 'mkv' or $xtargetuploadfilestype === 'mov' or $xtargetuploadfilestype === 'mp3' or $xtargetuploadfilestype === 'mp4' or $xtargetuploadfilestype === 'ogg' or $xtargetuploadfilestype === 'png' or $xtargetuploadfilestype === 'wav' or $xtargetuploadfilestype === 'webm') {
                                                $xuploadfilesbytesmax += $xuploadfilesbytessize;
                                                if ((xpathsize('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')) + $xuploadfilesbytesmax) > 104857600 && ($_FILES['xuploadfiles']['name'][$xkeys] > 10485760)) {
                                                    echo 'You do not have enough storage space !';
                                                    $xalert++;
                                                } else {
                                                    if (($xtargetuploadfilestype === 'gif' or $xtargetuploadfilestype === 'heic' or $xtargetuploadfilestype === 'heif' or $xtargetuploadfilestype === 'jpeg' or $xtargetuploadfilestype === 'jpg' or $xtargetuploadfilestype === 'png') && move_uploaded_file($_FILES['xuploadfiles']['tmp_name'][$xkeys], './' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/images' . $xtargetuploadfiles)) {
                                                        $xmefilespath .= '<br><div class="xdmsgi"><img class="ximsgi" src="./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/images' . $xtargetuploadfiles . '"><br><a class="xamsgi" href="./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/images' . $xtargetuploadfiles . '" target="_blank">./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/images' . $xtargetuploadfiles . '</a></div>';
                                                        if (file_exists('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/images' . $xtargetuploadfiles)) {
                                                            copy('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/images' . $xtargetuploadfiles, './' . $_POST['xreceiversubmitmessage'] . '/images' . $xtargetuploadfiles);
                                                            $xreceiverfilespath .= '<br><div class="xdmsgi"><img class="ximsgi" src="./' . $_POST['xreceiversubmitmessage'] . '/images' . $xtargetuploadfiles . '"><br><a class="xamsgi" href="./' . $_POST['xreceiversubmitmessage'] . '/images' . $xtargetuploadfiles . '" target="_blank">./' . $_POST['xreceiversubmitmessage'] . '/images' . $xtargetuploadfiles . '</a></div>';
                                                        }
                                                    } else if (($xtargetuploadfilestype === 'aac' or $xtargetuploadfilestype === 'mp3' or $xtargetuploadfilestype === 'ogg' or $xtargetuploadfilestype === 'wav') && move_uploaded_file($_FILES['xuploadfiles']['tmp_name'][$xkeys], './' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/audios' . $xtargetuploadfiles)) {
                                                        $xmefilespath .= '<br><a class="xamsga" href="./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/audios' . $xtargetuploadfiles . '" target="_blank">./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/audios' . $xtargetuploadfiles . '</a>';
                                                        if (file_exists('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/audios' . $xtargetuploadfiles)) {
                                                            copy('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/audios' . $xtargetuploadfiles, './' . $_POST['xreceiversubmitmessage'] . '/audios' . $xtargetuploadfiles);
                                                            $xreceiverfilespath .= '<br><a class="xamsga" href="./' . $_POST['xreceiversubmitmessage'] . '/audios' . $xtargetuploadfiles . '" target="_blank">./' . $_POST['xreceiversubmitmessage'] . '/audios' . $xtargetuploadfiles . '</a>';
                                                        }
                                                    } else if (($xtargetuploadfilestype === 'avi' or $xtargetuploadfilestype === 'hevc' or $xtargetuploadfilestype === 'mkv' or $xtargetuploadfilestype === 'mov' or $xtargetuploadfilestype === 'mp4' or $xtargetuploadfilestype === 'webm') && move_uploaded_file($_FILES['xuploadfiles']['tmp_name'][$xkeys], './' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/videos' . $xtargetuploadfiles)) {
                                                        $xmefilespath .= '<br><a class="xamsgv" href="./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/videos' . $xtargetuploadfiles . '" target="_blank">./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/videos' . $xtargetuploadfiles . '</a>';
                                                        if (file_exists('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/videos' . $xtargetuploadfiles)) {
                                                            copy('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/videos' . $xtargetuploadfiles, './' . $_POST['xreceiversubmitmessage'] . '/videos' . $xtargetuploadfiles);
                                                            $xreceiverfilespath .= '<br><a class="xamsgv" href="./' . $_POST['xreceiversubmitmessage'] . '/videos' . $xtargetuploadfiles . '" target="_blank">./' . $_POST['xreceiversubmitmessage'] . '/videos' . $xtargetuploadfiles . '</a>';
                                                        }
                                                    }
                                                }
                                            } else {
                                                echo 'Invalid format !';
                                                $xalert++;
                                            }
                                        }
                                    } else {
                                        echo 'More than 10 files are not allowed !';
                                        $xalert++;
                                    }
                                } else {
                                    echo 'Your file size is more than 100 MB !';
                                    $xalert++;
                                }
                            }

                            if ($xalert === 0) {

                                $xmemessagesselect = $xconnection->prepare("SELECT creationdate, messages, blocks FROM userstable WHERE username=?");
                                $xmemessagesselect->execute([htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                $xmemessagesselect = $xmemessagesselect->fetch(PDO::FETCH_ASSOC);
                                $xmemessages = xdecryptmessages($xmessages = $xmemessagesselect['messages'], $creationdate = $xmemessagesselect['creationdate']);
                                if ($xmemessages !== false) {

                                    $xreceivermessagesselect = $xconnection->prepare("SELECT creationdate, messages, blocks FROM userstable WHERE username=?");
                                    $xreceivermessagesselect->execute([$_POST['xreceiversubmitmessage']]);
                                    $xreceivermessagesselect = $xreceivermessagesselect->fetch(PDO::FETCH_ASSOC);
                                    if ($xreceivermessagesselect !== false) {
                                        $xreceivermessages = xdecryptmessages($xmessages = $xreceivermessagesselect['messages'], $creationdate = $xreceivermessagesselect['creationdate']);
                                    } else {
                                        $xreceivermessages = xdecryptmessages($xmessages = '', $creationdate = xdatetime());
                                    }
                                    if ($xreceivermessages !== false) {

                                        if (count(array_values(array_filter(explode('(p)', $xmemessages)))) <= 100) {

                                            if ($_POST['xreceiversubmitmessage'] !== htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')) {
                                                if ($xreceivermessagesselect !== false) {
                                                    if (strpos($xreceivermessagesselect['blocks'], '(b)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(bb)') === false) {
                                                        if (strpos($xmemessagesselect['blocks'], '(b)' . $_POST['xreceiversubmitmessage'] . '(bb)') === false) {

                                                            if ($xmemessages !== '') {
                                                                if (strpos($xmemessages, '(u)' . $_POST['xreceiversubmitmessage'] . '(uu)') === false) {
                                                                    $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=CONCAT(messages, ?) WHERE username=?");
                                                                    $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $xmefilespath . $xmessagesubmitmessage . '(mm)(d)' . $xdatetime . '(dd)(u)' . $_POST['xreceiversubmitmessage'] . '(uu)(cc)(pp)', $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                                } else {
                                                                    $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                                    $xmeupdatemessages->execute([xencryptmessages($xmessages = str_replace('(u)' . $_POST['xreceiversubmitmessage'] . '(uu)(cc)(pp)', '(u)' . $_POST['xreceiversubmitmessage'] . '(uu)(cc)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $xmefilespath . $xmessagesubmitmessage . '(mm)(d)' . $xdatetime . '(dd)(u)' . $_POST['xreceiversubmitmessage'] . '(uu)(cc)(pp)', $xmemessages), $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                                }
                                                            } else {
                                                                $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                                $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $xmefilespath . $xmessagesubmitmessage . '(mm)(d)' . $xdatetime . '(dd)(u)' . $_POST['xreceiversubmitmessage'] . '(uu)(cc)(pp)', $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                            }
                                                        } else {

                                                            if ($xmemessages !== '') {
                                                                if (strpos($xmemessages, '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)') === false) {
                                                                    $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=CONCAT(messages, ?) WHERE username=?");
                                                                    $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)You have blocked ' . $_POST['xreceiversubmitmessage'] . ' ! To unblock, send the word RM' . $_POST['xreceiversubmitmessage'] . ' in this conversation !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                                } else {
                                                                    $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                                    $xmeupdatemessages->execute([xencryptmessages($xmessages = str_replace('(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)You have blocked ' . $_POST['xreceiversubmitmessage'] . ' ! To unblock, send the word RM' . $_POST['xreceiversubmitmessage'] . ' in this conversation !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $xmemessages), $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                                }
                                                            } else {
                                                                $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                                $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)You have blocked ' . $_POST['xreceiversubmitmessage'] . ' ! To unblock, send the word RM' . $_POST['xreceiversubmitmessage'] . ' in this conversation !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                            }
                                                        }
                                                    } else {

                                                        if ($xmemessages !== '') {
                                                            if (strpos($xmemessages, '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)') === false) {
                                                                $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=CONCAT(messages, ?) WHERE username=?");
                                                                $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $_POST['xreceiversubmitmessage'] . ' blocked you !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                            } else {
                                                                $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                                $xmeupdatemessages->execute([xencryptmessages($xmessages = str_replace('(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $_POST['xreceiversubmitmessage'] . ' blocked you !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $xmemessages), $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                            }
                                                        } else {
                                                            $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                            $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $_POST['xreceiversubmitmessage'] . ' blocked you !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                        }
                                                    }
                                                } else {

                                                    if ($xmemessages !== '') {
                                                        if (strpos($xmemessages, '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)') === false) {
                                                            $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=CONCAT(messages, ?) WHERE username=?");
                                                            $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)User with username ' . $_POST['xreceiversubmitmessage'] . ' not found !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                        } else {
                                                            $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                            $xmeupdatemessages->execute([xencryptmessages($xmessages = str_replace('(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)User with username ' . $_POST['xreceiversubmitmessage'] . ' not found !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $xmemessages), $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                        }
                                                    } else {
                                                        $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                        $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)User with username ' . $_POST['xreceiversubmitmessage'] . ' not found !(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                                                    }
                                                }
                                            }

                                            if ($xreceivermessagesselect !== false) {
                                                if (strpos($xreceivermessagesselect['blocks'], '(b)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(bb)') === false) {
                                                    if (strpos($xmemessagesselect['blocks'], '(b)' . $_POST['xreceiversubmitmessage'] . '(bb)') === false) {

                                                        if ($xreceivermessages !== '') {
                                                            if (strpos($xreceivermessages, '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)') === false) {
                                                                $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=CONCAT(messages, ?) WHERE username=?");
                                                                $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $xreceiverfilespath . $xmessagesubmitmessage . '(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $creationdate = $xreceivermessagesselect['creationdate']), $_POST['xreceiversubmitmessage']]);
                                                            } else {
                                                                $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                                $xmeupdatemessages->execute([xencryptmessages($xmessages = str_replace('(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $xreceiverfilespath . $xmessagesubmitmessage . '(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $xreceivermessages), $creationdate = $xreceivermessagesselect['creationdate']), $_POST['xreceiversubmitmessage']]);
                                                            }
                                                        } else {
                                                            $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                            $xmeupdatemessages->execute([xencryptmessages($xmessages = '(p)(c)(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)(m)' . $xreceiverfilespath . $xmessagesubmitmessage . '(mm)(d)' . $xdatetime . '(dd)(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)(cc)(pp)', $creationdate = $xreceivermessagesselect['creationdate']), $_POST['xreceiversubmitmessage']]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $xconnection = null;
                                        return false;
                                    }
                                } else {
                                    $xconnection = null;
                                    return false;
                                }
                            }
                        }
                        $xconnection = null;
                    }
                }
            }
        } else {
            $_SESSION['user-username'][2] = time();
        }
    }

    if (isset($_POST['xreceivemessages']) && !empty($_POST['xreceivemessages']) && preg_match('/^[a-z]+$/', $_POST['xreceivemessages']) && $_POST['xreceivemessages'] === 'xtrue') {
        if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {

            $xdatetime = xdatetime();

            $xmessagesselect = $xconnection->prepare("SELECT creationdate, messages FROM userstable WHERE username=?");
            $xmessagesselect->execute([htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
            $xmessagesselect = $xmessagesselect->fetch(PDO::FETCH_ASSOC);
            $xmemessages = xdecryptmessages($xmessages = $xmessagesselect['messages'], $creationdate = $xmessagesselect['creationdate']);
            if ($xmemessages !== false) {

                if ($xmessagesselect['messages'] !== '') {
                    if (strpos($xmemessages, '(p)(pp)') !== false) {
                        $xreceiveupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                        $xreceiveupdatemessages->execute([xencryptmessages($xmessages = str_replace('(p)(pp)', '', $xmemessages), $creationdate = $xmessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                    } else {

                        $xchilds = [];
                        $xmessagedisplay = '';

                        $xparent = array_values(array_filter(explode('(p)', $xmemessages)));

                        for ($i = 0; $i < count($xparent); $i++) {
                            $xchild = array_values(array_filter(explode('(c)', $xparent[$i])));

                            $xusername = substr($xparent[$i], (strpos($xparent[$i], '(u)') + 3), (strpos($xparent[$i], '(uu)', (strpos($xparent[$i], '(u)') + 3))) - (strpos($xparent[$i], '(u)') + 3));

                            $xuserreceivemessageselect = $xconnection->prepare("SELECT creationdate, messages, status FROM userstable WHERE username=?");
                            $xuserreceivemessageselect->execute([$xusername]);
                            $xuserreceivemessageselect = $xuserreceivemessageselect->fetch(PDO::FETCH_ASSOC);
                            $xreceivermessages = xdecryptmessages($xmessages = $xuserreceivemessageselect['messages'], $creationdate = $xuserreceivemessageselect['creationdate']);
                            if ($xreceivermessages !== false) {

                                $xunreads = 0;
                                $xstatus = 'offline';
                                $xparent2 = array_values(array_filter(explode('(p)', $xreceivermessages)));

                                for ($k = count($xparent2) - 1; $k >= 0; $k--) {

                                    if (strpos($xparent2[$k], '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)') !== false) {

                                        $xchild2 = array_values(array_filter(explode('(c)', $xparent2[$k])));
                                        for ($l = count($xchild2) - 1; $l >= 0; $l--) {
                                            if (strpos($xchild2[$l], '(w)' . $xusername . '(ww)') !== false) {
                                                $xdate2 = substr($xchild2[$l], (strpos($xchild2[$l], '(d)') + 3), (strpos($xchild2[$l], '(dd)', (strpos($xchild2[$l], '(d)') + 3))) - (strpos($xchild2[$l], '(d)') + 3));
                                                if (strpos($xdate2, '✓') === false) {
                                                    $xunreads++;
                                                }
                                            }
                                        }

                                        break;
                                    }
                                }

                                if (abs(strtotime($xuserreceivemessageselect['status'])) - abs(strtotime($xdatetime)) < 2) {
                                    $xstatus = 'status';
                                }
                                $xchilds[$i] = end($xchild) . '(r)' . $xunreads . '(rr)(n)' . count($xchild) . '(nn)(o)' . $xstatus . '(oo)';
                            } else {
                                $xconnection = null;
                                return false;
                            }
                        }

                        $xcompare = function ($xa, $xb) {
                            if (strpos($xa, '✓') !== false) {
                                $xa = str_replace('✓ ', '', $xa);
                            }
                            if (strpos($xb, '✓') !== false) {
                                $xb = str_replace('✓ ', '', $xb);
                            }
                            $xadt = strtotime(explode('(dd)', explode('(d)', $xa)[1])[0]);
                            $xbdt = strtotime(explode('(dd)', explode('(d)', $xb)[1])[0]);
                            if ($xadt > $xbdt) {
                                return -1;
                            } elseif ($xadt < $xbdt) {
                                return 1;
                            } else {
                                return 0;
                            }
                        };
                        usort($xchilds, $xcompare);

                        for ($j = 0; $j < count($xchilds); $j++) {
                            $xusername = substr($xchilds[$j], (strpos($xchilds[$j], '(u)') + 3), (strpos($xchilds[$j], '(uu)', (strpos($xchilds[$j], '(u)') + 3))) - (strpos($xchilds[$j], '(u)') + 3));
                            $xmessage = substr($xchilds[$j], (strpos($xchilds[$j], '(m)') + 3), (strpos($xchilds[$j], '(mm)', (strpos($xchilds[$j], '(m)') + 3))) - (strpos($xchilds[$j], '(m)') + 3));
                            $xdate = substr($xchilds[$j], (strpos($xchilds[$j], '(d)') + 3), (strpos($xchilds[$j], '(dd)', (strpos($xchilds[$j], '(d)') + 3))) - (strpos($xchilds[$j], '(d)') + 3));
                            $xunread = substr($xchilds[$j], (strpos($xchilds[$j], '(r)') + 3), (strpos($xchilds[$j], '(rr)', (strpos($xchilds[$j], '(r)') + 3))) - (strpos($xchilds[$j], '(r)') + 3));
                            if ($xunread === '' || $xunread === '0') {
                                $xunread = '';
                            } else {
                                $xunread = '<div class="xbfmu">' . $xunread . '</div>';
                            }
                            $xnumbermsgs = substr($xchilds[$j], (strpos($xchilds[$j], '(n)') + 3), (strpos($xchilds[$j], '(nn)', (strpos($xchilds[$j], '(n)') + 3))) - (strpos($xchilds[$j], '(n)') + 3));
                            $xstatus = substr($xchilds[$j], (strpos($xchilds[$j], '(o)') + 3), (strpos($xchilds[$j], '(oo)', (strpos($xchilds[$j], '(o)') + 3))) - (strpos($xchilds[$j], '(o)') + 3));
                            if ($xstatus === 'status') {
                                $xstatus = 'xon';
                            } else {
                                $xstatus = 'xoff';
                            }
                            $xtick = '';
                            if (strpos($xdate, '✓') !== false) {
                                $xdate = str_replace('✓ ', '', $xdate);
                                $xtick = '✓ ';
                            }

                            if (explode(' ', $xdate)[0] === explode(' ', $xdatetime)[0]) {
                                $xmessagedisplay .= '<div class="xbfm" datavalue="' . $xusername . '">'
                                        . '<div class="xbfmd"><div class="xbfma"><img class="xbfmimg" src="./' . $xusername . '/imageaccount.png?t=' . filemtime('./' . $xusername . '/imageaccount.png') . '"><div class="xbfms ' . $xstatus . '"></div></div></div>'
                                        . '<div class="xbfmd">' . $xusername . '</div>'
                                        . '<div class="xellipsis">' . $xmessage . '</div>'
                                        . '<div class="xbfmd xbfmt">' . $xtick . explode(' ', $xdate)[1] . '</div>'
                                        . '<div class="xbfmn">' . $xnumbermsgs . '</div>'
                                        . $xunread
                                        . '</div>';
                            } else {
                                $xmessagedisplay .= '<div class="xbfm" datavalue="' . $xusername . '">'
                                        . '<div class="xbfmd"><div class="xbfma"><img class="xbfmimg" src="./' . $xusername . '/imageaccount.png?t=' . filemtime('./' . $xusername . '/imageaccount.png') . '"><div class="xbfms ' . $xstatus . '"></div></div></div>'
                                        . '<div class="xbfmd">' . $xusername . '</div>'
                                        . '<div class="xellipsis">' . $xmessage . '</div>'
                                        . '<div class="xbfmd xbfmt">' . $xtick . $xdate . '</div>'
                                        . '<div class="xbfmn">' . $xnumbermsgs . '</div>'
                                        . $xunread
                                        . '</div>';
                            }
                        }
                        echo $xmessagedisplay;
                    }

                    $xmeupdateonline = $xconnection->prepare("UPDATE userstable SET status=? WHERE username=?");
                    $xmeupdateonline->execute([$xdatetime, htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                } else {
                    echo '<div class="xempty">Empty !</div>';
                }
            } else {
                $xconnection = null;
                return false;
            }
            $xconnection = null;
        }
    }

    if (isset($_POST['xreceivemessage']) && !empty($_POST['xreceivemessage']) && preg_match('/^[a-z]+$/', $_POST['xreceivemessage']) && $_POST['xreceivemessage'] === 'xtrue') {
        if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['xuserreceivemessage']) && preg_match('/^[a-z0-9]+$/', $_POST['xuserreceivemessage'])) {

                $xdatetime = xdatetime();

                $xmemessagesselect = $xconnection->prepare("SELECT creationdate, messages, status FROM userstable WHERE username=?");
                $xmemessagesselect->execute([htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                $xmemessagesselect = $xmemessagesselect->fetch(PDO::FETCH_ASSOC);
                $xmemessages = xdecryptmessages($xmessages = $xmemessagesselect['messages'], $creationdate = $xmemessagesselect['creationdate']);
                if ($xmemessages !== false) {

                    $xmestatus = 'xoff';
                    if (abs(strtotime($xmemessagesselect['status'])) - abs(strtotime($xdatetime)) < 2) {
                        $xmestatus = 'xon';
                    }

                    $xreceivermessagesselect = $xconnection->prepare("SELECT creationdate, messages, status FROM userstable WHERE username=?");
                    $xreceivermessagesselect->execute([$_POST['xuserreceivemessage']]);
                    $xreceivermessagesselect = $xreceivermessagesselect->fetch(PDO::FETCH_ASSOC);
                    $xreceivermessages = xdecryptmessages($xmessages = $xreceivermessagesselect['messages'], $creationdate = $xreceivermessagesselect['creationdate']);
                    if ($xreceivermessages !== false) {

                        $xreceiverstatus = 'xoff';
                        if (abs(strtotime($xreceivermessagesselect['status'])) - abs(strtotime($xdatetime)) < 2) {
                            $xreceiverstatus = 'xon';
                        }

                        $xpreusername = '';
                        $xmessagedisplay = '';

                        if (strpos($xmemessages, '(p)(pp)') !== false) {
                            $xreceiveupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                            $xreceiveupdatemessages->execute([xencryptmessages($xmessages = str_replace('(p)(pp)', '', $xmemessages), $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                        } else {

                            $xparent = array_values(array_filter(explode('(p)', $xmemessages)));

                            for ($i = count($xparent) - 1; $i >= 0; $i--) {

                                if (strpos($xparent[$i], '(u)' . $_POST['xuserreceivemessage'] . '(uu)') !== false) {

                                    $xchild = array_values(array_filter(explode('(c)', $xparent[$i])));
                                    $xcount = count($xchild);

                                    for ($j = $xcount - 1; $j >= 0; $j--) {
                                        $xsbr = '';
                                        $xsbm = 'xmt1';
                                        if ($j === $xcount - 1) {
                                            $xsbm = 'xmt2';
                                        }
                                        $xusername = substr($xchild[$j], (strpos($xchild[$j], '(w)') + 3), (strpos($xchild[$j], '(ww)', (strpos($xchild[$j], '(w)') + 3))) - (strpos($xchild[$j], '(w)') + 3));
                                        $xmessage = substr($xchild[$j], (strpos($xchild[$j], '(m)') + 3), (strpos($xchild[$j], '(mm)', (strpos($xchild[$j], '(m)') + 3))) - (strpos($xchild[$j], '(m)') + 3));
                                        $xdate = substr($xchild[$j], (strpos($xchild[$j], '(d)') + 3), (strpos($xchild[$j], '(dd)', (strpos($xchild[$j], '(d)') + 3))) - (strpos($xchild[$j], '(d)') + 3));
                                        $xdate2 = $xdate;
                                        $xtick = '';
                                        if (strpos($xdate, '✓') !== false) {
                                            $xdate = str_replace('✓ ', '', $xdate);
                                            $xdate2 = str_replace('✓ ', '', $xdate);
                                            $xtick = '✓ ';
                                        }

                                        if ($xusername === htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')) {

                                            if (explode(' ', $xdate)[0] === explode(' ', $xdatetime)[0]) {

                                                if ($xpreusername === $xusername) {
                                                    $xsbr = 'xbr';
                                                } else {
                                                    $xsbr = $xsbm;
                                                }
                                                $xdate = explode(' ', $xdate)[1];
                                            } else {

                                                if ($xpreusername === $xusername) {
                                                    $xsbr = 'xbr';
                                                } else {
                                                    $xsbr = $xsbm;
                                                }
                                            }

                                            $xmessagedisplay .= '<div class="xbfmm ' . $xsbr . '" id="xm' . str_replace(['-', ' ', ':'], '', $xdate2) . '" datavalue="' . $xdate2 . '">'
                                                    . '<div class="xbfmd"><div class="xbfma"><img class="xbfmimg" src="./' . $xusername . '/imageaccount.png?t=' . filemtime('./' . $xusername . '/imageaccount.png') . '"><div class="xbfms ' . $xmestatus . '"></div></div></div>'
                                                    . '<div class="xbfmd">' . $xusername . ' :</div>'
                                                    . '<div class="xbfmcm">' . $xmessage . '</div>'
                                                    . '<div class="xbfmdm xfz">' . $xtick . $xdate . '</div>'
                                                    . '</div>';
                                        } else {

                                            if (explode(' ', $xdate)[0] === explode(' ', $xdatetime)[0]) {

                                                if ($xpreusername === $xusername) {
                                                    $xsbr = 'xbr';
                                                } else {
                                                    $xsbr = $xsbm;
                                                }
                                                $xdate = explode(' ', $xdate)[1];
                                            } else {

                                                if ($xpreusername === $xusername) {
                                                    $xsbr = 'xbr';
                                                } else {
                                                    $xsbr = $xsbm;
                                                }
                                            }

                                            $xmessagedisplay .= '<div class="xbfmr ' . $xsbr . '">'
                                                    . '<div class="xbfmdr"><div class="xbfma"><img class="xbfmimg" src="./' . $xusername . '/imageaccount.png?t=' . filemtime('./' . $xusername . '/imageaccount.png') . '"><div class="xbfms ' . $xreceiverstatus . '"></div></div></div>'
                                                    . '<div class="xbfmdr">' . $xusername . ' :</div>'
                                                    . '<div class="xbfmcr">' . $xmessage . '</div>'
                                                    . '<div class="xbfmd xfz">' . $xtick . $xdate . '</div>'
                                                    . '</div>';
                                        }

                                        $xpreusername = $xusername;
                                    }

                                    break;
                                }
                            }
                            echo $xmessagedisplay;
                        }

                        $xoldparent = '';
                        $xparent = array_values(array_filter(explode('(p)', $xreceivermessages)));

                        for ($i = count($xparent) - 1; $i >= 0; $i--) {

                            if (strpos($xparent[$i], '(u)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(uu)') !== false) {
                                $xoldparent = $xparent[$i];

                                $xchild = array_values(array_filter(explode('(c)', $xparent[$i])));
                                for ($j = count($xchild) - 1; $j >= 0; $j--) {
                                    if (strpos($xchild[$j], '(w)' . $_POST['xuserreceivemessage'] . '(ww)') !== false) {
                                        $xdate = substr($xchild[$j], (strpos($xchild[$j], '(d)') + 3), (strpos($xchild[$j], '(dd)', (strpos($xchild[$j], '(d)') + 3))) - (strpos($xchild[$j], '(d)') + 3));
                                        if (strpos($xdate, '✓') === false) {
                                            $xparent[$i] = str_replace($xdate, '✓ ' . $xdate, $xparent[$i]);

                                            $xreceiveupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                            $xreceiveupdatemessages->execute([xencryptmessages($xmessages = str_replace($xoldparent, $xparent[$i], $xreceivermessages), $creationdate = $xreceivermessagesselect['creationdate']), $_POST['xuserreceivemessage']]);
                                        }
                                    }
                                }

                                break;
                            }
                        }

                        $xmeupdateonline = $xconnection->prepare("UPDATE userstable SET status=? WHERE username=?");
                        $xmeupdateonline->execute([$xdatetime, htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                    } else {
                        $xconnection = null;
                        return false;
                    }
                } else {
                    $xconnection = null;
                    return false;
                }
                $xconnection = null;
            }
        }
    }

    if (isset($_POST['xdelmsg']) && !empty($_POST['xdelmsg']) && preg_match('/^[a-z]+$/', $_POST['xdelmsg']) && $_POST['xdelmsg'] === 'xtrue') {
        if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['xdelmsgdate']) && preg_match('/^[0-9: -]+$/', $_POST['xdelmsgdate'])) {

                $xmemessagesselect = $xconnection->prepare("SELECT creationdate, messages FROM userstable WHERE username=?");
                $xmemessagesselect->execute([htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                $xmemessagesselect = $xmemessagesselect->fetch(PDO::FETCH_ASSOC);
                $xmemessages = xdecryptmessages($xmessages = $xmemessagesselect['messages'], $creationdate = $xmemessagesselect['creationdate']);
                if ($xmemessages !== false) {

                    $xparent = array_values(array_filter(explode('(p)', $xmemessages)));

                    for ($i = count($xparent) - 1; $i >= 0; $i--) {

                        if ((strpos($xparent[$i], '(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)') !== false) && (strpos($xparent[$i], $_POST['xdelmsgdate']) !== false)) {

                            $xchild = array_values(array_filter(explode('(c)', $xparent[$i])));
                            $xcountchild = count($xchild);

                            for ($j = $xcountchild - 1; $j >= 0; $j--) {
                                if ((strpos($xchild[$j], '(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)') !== false) && (strpos($xchild[$j], $_POST['xdelmsgdate']) !== false)) {

                                    if ($xcountchild === 1) {
                                        $xfind = '(p)(c)' . $xchild[$j];
                                    } else {
                                        $xchild[$j] = str_replace('(pp)', '', $xchild[$j]);
                                        $xfind = '(c)' . $xchild[$j];
                                    }

                                    $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                    $xmeupdatemessages->execute([xencryptmessages($xmessages = str_replace($xfind, '', $xmemessages), $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);

                                    $xdelfiles = $xfind;
                                    while (strpos($xdelfiles, 'target="_blank"') !== false) {
                                        $xopenflag = strpos($xdelfiles, 'href="') + 6;
                                        $xcloseflag = strpos($xdelfiles, '" target', $xopenflag);
                                        $xsubstring = substr($xdelfiles, $xopenflag, $xcloseflag - $xopenflag);
                                        if (file_exists($xsubstring)) {
                                            unlink($xsubstring);
                                        }
                                        $xdelfiles = explode($xsubstring . '</a>', $xdelfiles)[1];
                                    }

                                    $xreceiverusername = substr($xchild[$j], (strpos($xchild[$j], '(u)') + 3), (strpos($xchild[$j], '(uu)', (strpos($xchild[$j], '(u)') + 3))) - (strpos($xchild[$j], '(u)') + 3));
                                    if ($xreceiverusername !== htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')) {

                                        $xreceivermessagesselect = $xconnection->prepare("SELECT creationdate, messages FROM userstable WHERE username=?");
                                        $xreceivermessagesselect->execute([$xreceiverusername]);
                                        $xreceivermessagesselect = $xreceivermessagesselect->fetch(PDO::FETCH_ASSOC);
                                        $xreceivermessages = xdecryptmessages($xmessages = $xreceivermessagesselect['messages'], $creationdate = $xreceivermessagesselect['creationdate']);
                                        if ($xreceivermessages !== false) {

                                            $xparent = array_values(array_filter(explode('(p)', $xreceivermessages)));

                                            for ($i = count($xparent) - 1; $i >= 0; $i--) {

                                                if ((strpos($xparent[$i], '(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)') !== false) && (strpos($xparent[$i], $_POST['xdelmsgdate']) !== false)) {

                                                    $xchild = array_values(array_filter(explode('(c)', $xparent[$i])));
                                                    $xcountchild = count($xchild);

                                                    for ($j = $xcountchild - 1; $j >= 0; $j--) {
                                                        if ((strpos($xchild[$j], '(w)' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '(ww)') !== false) && (strpos($xchild[$j], $_POST['xdelmsgdate']) !== false)) {

                                                            if ($xcountchild === 1) {
                                                                $xfind = '(p)(c)' . $xchild[$j];
                                                            } else {
                                                                $xchild[$j] = str_replace('(pp)', '', $xchild[$j]);
                                                                $xfind = '(c)' . $xchild[$j];
                                                            }

                                                            $xreceiverupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                                                            $xreceiverupdatemessages->execute([xencryptmessages($xmessages = str_replace($xfind, '', $xreceivermessages), $creationdate = $xreceivermessagesselect['creationdate']), $xreceiverusername]);

                                                            $xdelfiles = $xfind;
                                                            while (strpos($xdelfiles, 'target="_blank"') !== false) {
                                                                $xopenflag = strpos($xdelfiles, 'href="') + 6;
                                                                $xcloseflag = strpos($xdelfiles, '" target', $xopenflag);
                                                                $xsubstring = substr($xdelfiles, $xopenflag, $xcloseflag - $xopenflag);
                                                                if (file_exists($xsubstring)) {
                                                                    unlink($xsubstring);
                                                                }
                                                                $xdelfiles = explode($xsubstring . '</a>', $xdelfiles)[1];
                                                            }

                                                            break;
                                                        }
                                                    }

                                                    break;
                                                }
                                            }
                                        } else {
                                            $xconnection = null;
                                            return false;
                                        }
                                    }

                                    break;
                                }
                            }

                            break;
                        }
                    }
                } else {
                    $xconnection = null;
                    return false;
                }
                $xconnection = null;
            }
        }
    }

    if (isset($_POST['xdelmsgs']) && !empty($_POST['xdelmsgs']) && preg_match('/^[a-z]+$/', $_POST['xdelmsgs']) && $_POST['xdelmsgs'] === 'xtrue') {
        if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['xdelmsgsusername']) && preg_match('/^[a-z0-9]+$/', $_POST['xdelmsgsusername'])) {

                $xmemessagesselect = $xconnection->prepare("SELECT creationdate, messages FROM userstable WHERE username=?");
                $xmemessagesselect->execute([htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                $xmemessagesselect = $xmemessagesselect->fetch(PDO::FETCH_ASSOC);
                $xmemessages = xdecryptmessages($xmessages = $xmemessagesselect['messages'], $creationdate = $xmemessagesselect['creationdate']);
                if ($xmemessages !== false) {

                    $xparent = array_values(array_filter(explode('(p)', $xmemessages)));

                    for ($i = count($xparent) - 1; $i >= 0; $i--) {

                        if (strpos($xparent[$i], '(u)' . $_POST['xdelmsgsusername'] . '(uu)') !== false) {

                            $xmeupdatemessages = $xconnection->prepare("UPDATE userstable SET messages=? WHERE username=?");
                            $xmeupdatemessages->execute([xencryptmessages($xmessages = str_replace('(p)' . $xparent[$i], '', $xmemessages), $creationdate = $xmemessagesselect['creationdate']), htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);

                            $xdelfiles = $xparent[$i];
                            while (strpos($xdelfiles, 'target="_blank"') !== false) {
                                $xopenflag = strpos($xdelfiles, 'href="') + 6;
                                $xcloseflag = strpos($xdelfiles, '" target', $xopenflag);
                                $xsubstring = substr($xdelfiles, $xopenflag, $xcloseflag - $xopenflag);
                                if (file_exists($xsubstring)) {
                                    unlink($xsubstring);
                                }
                                $xdelfiles = explode($xsubstring . '</a>', $xdelfiles)[1];
                            }

                            break;
                        }
                    }
                } else {
                    $xconnection = null;
                    return false;
                }
                $xconnection = null;
            }
        }
    }

    if (isset($_POST['xblockuser']) && !empty($_POST['xblockuser']) && preg_match('/^[a-z]+$/', $_POST['xblockuser']) && $_POST['xblockuser'] === 'xtrue') {
        if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['xblockuserusername']) && preg_match('/^[a-z0-9]+$/', $_POST['xblockuserusername']) && $_POST['xblockuserusername'] !== htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')) {

                $xmeupdateblocks = $xconnection->prepare("UPDATE userstable SET blocks=CONCAT(blocks, ?) WHERE username=?");
                $xmeupdateblocks->execute(['(b)' . $_POST['xblockuserusername'] . '(bb)', htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8')]);
                $xconnection = null;
            }
        }
    }

    if (isset($_POST['xlogout']) && !empty($_POST['xlogout']) && preg_match('/^[a-z]+$/', $_POST['xlogout']) && $_POST['xlogout'] === 'xtrue') {
        if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION = [];
            session_destroy();
        }
    }
}
?>
