<?php

require_once '../../config.php';
require_once './script2.php';

use root\common\src\script2\Functions;

if (xauthentication()) {

    function xdatasize($xconnection, $username) {

        $xselect1 = $xconnection->prepare("SELECT LENGTH(messages) FROM userstable WHERE username=? AND username2=?");
        $xselect1->execute([$username, $_SESSION['user-username'][5]]);
        return $xselect1->fetchColumn();
    }

    function xcontentsize($path, $ext = []) {

        $totalSize = 0;
        $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($path), RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($dir as $file) {
            if ($file->isFile() && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $ext))
                $totalSize += $file->getSize();
        }
        return $totalSize;
    }

    function formatBytes($bytes, $precision = 2) {

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    if (isset($_POST['xcirclegraph']) && !empty($_POST['xcirclegraph']) && preg_match('/^[a-z]+$/', $_POST['xcirclegraph']) && $_POST['xcirclegraph'] === "xtrue" && empty($_FILES)) {
        if (xservercheck() && $_SESSION['user-username'][6] === 'index.php') {

            $xfunction = new Functions();

            $xtotalspace = 104857600;
            $xaudiosize = xcontentsize('../../' . $_SESSION['user-username'][0], ['mp3', 'ogg', 'wav', 'webm']);
            $ximagesize = xcontentsize('../../' . $_SESSION['user-username'][0], ['gif', 'jpeg', 'jpg', 'png', 'webp']);
            $xvideosize = xcontentsize('../../' . $_SESSION['user-username'][0], ['mp4', 'ogv', 'webm']);
            $xusedspace = $xaudiosize + $ximagesize + $xvideosize;
            $xfreespace = $xtotalspace - $xusedspace;

            $xaudioangle = 360 * ($xaudiosize / $xtotalspace);
            $ximageangle = 360 * ($ximagesize / $xtotalspace);
            $xvideoangle = 360 * ($xvideosize / $xtotalspace);
            $xfreeangle = 360 * ($xfreespace / $xtotalspace);

            $xaccountimage = $xfunction->xaccountimage('../../' . $_SESSION['user-username'][0]);

            echo json_encode(array("xaudioangle" => $xaudioangle, "xaudiosize" => formatBytes($xaudiosize), "ximageangle" => $ximageangle, "ximagesize" => formatBytes($ximagesize), "xvideoangle" => $xvideoangle, "xvideosize" => formatBytes($xvideosize), "xfreeangle" => $xfreeangle, "xfreespace" => formatBytes($xfreespace), "xaccountimage" => substr($xaccountimage, 4) . "?t=" . filemtime($xaccountimage)));
        }
    }

    if (isset($_POST['xuploadimage']) && !empty($_POST["xuploadimage"]) && preg_match("/^[a-z]+$/", $_POST["xuploadimage"]) && $_POST["xuploadimage"] === "xtrue") {
        if (xservercheck() && $_SESSION['user-username'][6] === 'index.php') {
            if (isset($_FILES['ximagetoupload']) && isset($_FILES['ximagetoupload']['tmp_name']) && is_uploaded_file($_FILES['ximagetoupload']['tmp_name']) && $_FILES['ximagetoupload']['size'] < 10485760) {

                try {

                    $xconnection = new Connection();
                    $xconnection = $xconnection->xconnection();

                    $xfunction = new Functions();

                    if (104857600 - (xcontentsize('../../' . $_SESSION['user-username'][0], ['gif', 'jpeg', 'jpg', 'mp3', 'mp4', 'ogg', 'ogv', 'png', 'wav', 'webm', 'webp']) + xdatasize($xconnection, $_SESSION['user-username'][0])) > $_FILES['ximagetoupload']['size']) {

                        $ximgmime = mime_content_type($_FILES['ximagetoupload']['tmp_name']);
                        $ximgext = strtolower(pathinfo($_FILES['ximagetoupload']['name'], PATHINFO_EXTENSION));

                        if (in_array($ximgmime, ['image/gif', 'image/jpeg', 'image/png', 'image/webp']) && in_array($ximgext, ['gif', 'jpeg', 'jpg', 'png', 'webp'])) {

                            $xoldimg = $xfunction->xaccountimage('../../' . $_SESSION['user-username'][0]);
                            $xnewimg = '../../' . $_SESSION['user-username'][0] . "/accountimage.$ximgext";
                            if (unlink($xoldimg) && move_uploaded_file($_FILES['ximagetoupload']['tmp_name'], $xnewimg)) {
                                echo json_encode(array("xmessage" => "$xnewimg?t=" . filemtime($xnewimg), "xtype" => "#239f40"));
                            } else {
                                echo json_encode(array("xmessage" => "Not done !", "xtype" => "#ffa500"));
                            }
                        } else {
                            echo json_encode(array("xmessage" => "Invalid format !", "xtype" => "#ffa500"));
                        }
                    } else {
                        echo json_encode(array("xmessage" => "There is not enough space !", "xtype" => "#ffa500"));
                    }
                } catch (Throwable $e) {
                    echo json_encode(array("xmessage" => "Unfortunately, there is a problem !", "xtype" => "#ffa500"));
                } finally {
                    $xconnection = null;
                }
            } else {
                echo json_encode(array("xmessage" => "Just choose one image !", "xtype" => "#ffa500"));
            }
        } else {
            echo json_encode(array("xmessage" => "Not possible !", "xtype" => "#ffa500"));
        }
    }

    if (isset($_POST['xlogout']) && !empty($_POST['xlogout']) && preg_match('/^[a-z]+$/', $_POST['xlogout']) && $_POST['xlogout'] === "xtrue" && empty($_FILES)) {
        if (xservercheck() && $_SESSION['user-username'][6] === 'index.php') {

            $_SESSION = [];
            session_destroy();
            echo 'Logout !';
        }
    }
}
