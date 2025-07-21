<?php
include_once './config.php';

function xgeneratecsrftoken() {
    if (!isset($_SESSION['xtokenlogin'])) {
        $_SESSION['xtokenlogin'] = array(bin2hex(random_bytes(64)), 1);
    }
}

xgeneratecsrftoken();
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
    <head>
        <title>Messenger</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="messenger">
        <meta name="robots" content="index, follow">
        <meta name="description" content="This project was developed by @masoudiofficial, all the code in the messages file is the result of his ideas and creativity.">
        <link rel="canonical" href="http://localhost/messenger/">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20,400,0,0"/>
        <link rel="stylesheet" type="text/css" href="./styles.css?t=<?php echo filemtime('./styles.css'); ?>">
        <link rel="icon" type="image/png" href="./imageaccount.png?t=<?php echo filemtime('./imageaccount.png'); ?>">
    </head>
    <body>

        <div class="xalertmessage"></div>
        <script nonce="<?php echo $xnonce; ?>">
            function xalertmessage(xmessage, xtype) {
                document.querySelector('.xalertmessage').innerText = xmessage;
                document.querySelector('.xalertmessage').style.background = xtype;
                document.querySelector('.xalertmessage').style.display = 'block';
                setTimeout(() => document.querySelector('.xalertmessage').style.display = 'none', 5000);
            }
        </script>

        <div class="xblock">

            <div class="xpbar">
                <div class="xcbar xmessenger">Messenger</div>
                <div class="xcbar xnumbermessages"></div>
            </div>

            <div class="xscrollbar xboxes">

                <?php
                if (isset($_SESSION['user-username'], $_SESSION['user-info']) && (htmlspecialchars(strip_tags($_SESSION['user-info'][0]), ENT_QUOTES, 'UTF-8') === $_SERVER['REMOTE_ADDR']) && (htmlspecialchars(strip_tags($_SESSION['user-info'][1]), ENT_QUOTES, 'UTF-8') === $_SERVER['HTTP_USER_AGENT']) && (time() - htmlspecialchars(strip_tags($_SESSION['user-info'][2]), ENT_QUOTES, 'UTF-8') <= 86400) && (htmlspecialchars(strip_tags($_SESSION['user-username'][3]), ENT_QUOTES, 'UTF-8') === htmlspecialchars(strip_tags($_SESSION['user-info'][3]), ENT_QUOTES, 'UTF-8'))) {
                    ?>

                    <div class="xverticalscroll xsettings" id="xsettings">
                        <div class="xwelcome"></div>
                        <div class="xdivision">
                            <div class="ximageaccount">Image account :</div>
                            <div class="ximageaccountdiv"><img class="xselectimage" id="xselectimage" src="<?php echo './' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/imageaccount.png?t=' . filemtime('./' . htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8') . '/imageaccount.png'); ?>"></div>
                            <input type="file" class="ximagetoupload" id="ximagetoupload" accept=".gif, .heic, .heif, .jpeg, .jpg, .png">
                            <div class="xallowedformats">allowed formats : gif - heic - heif - jpeg - jpg - png</div>
                            <button type="button" class="xuploadimage" id="xuploadimage" disabled>Choose file</button>
                        </div>
                        <div class="xdivision">
                            <div class="xstoragespace">Storage space :</div>
                            <div class="xlabels"><div class="xcirclegraph"></div></div>
                        </div>
                        <div class="xlogout"></div>
                    </div>

                    <div class="xverticalscroll xmessages" id="xmessages"></div>
                    <div class="xverticalscroll xemojimessages" id="xemojimessages">
                        <div class="xbuttonsemojimessages" id="xbuttonsemojimessages"></div>
                    </div>

                    <div class="xbox">
                        <div class="xtextarea">
                            <textarea class="xverticalscroll xmessage" id="xmessage" maxlength="1000" spellcheck="false" placeholder="Message ..." autocomplete="off"></textarea>
                            <div class="xlength" id="xlength">1000</div>
                        </div>
                        <div class="xhorizontalscroll xbar">
                            <button type="button" class="xbuttons xsettingsbtn" id="xsettingsbtn"><span class="material-symbols-rounded">settings</span></button>
                            <input type="text" class="xinputs" id="xreceiver" maxlength="32" spellcheck="false" placeholder="Username ( receiver )" autocomplete="off">
                            <button type="button" class="xbuttons" id="xemoji"><span class="material-symbols-rounded">add_reaction</span></button>
                            <button type="button" class="xbuttons" id="xuploadfilesbutton"><span class="material-symbols-rounded">attach_file</span></button>
                            <input type="file" class="xuploadfiles" id="xuploadfiles" accept=".aac, .avi, .gif, .heic, .heif, .hevc, .jpeg, .jpg, .mkv, .mov, .mp3, .mp4, .ogg, .png, .wav, .webm" multiple>
                            <button type="submit" class="xbuttons xsubmit" id="xsubmit"><span class="material-symbols-rounded">send</span></button>
                            <button type="button" class="xbuttons xopenoptions" id="xopenoptions"><span class="material-symbols-rounded">more_vert</span></button>
                        </div>
                    </div>

                    <div class="xhorizontalscroll xbf xactionsbar" id="xactionsbar">
                        <input type="button" class="xbuttons xcloseoptions" id="xcloseoptions" value="✕">
                        <input type="button" class="xbuttons xbackmsg" id="xbackmsg" value="Back">
                        <input type="hidden" id="xdelmsgdate" autocomplete="off">
                        <input type="button" class="xbuttons xdelmsg" id="xdelmsg" value="Delete message">
                        <input type="button" class="xbuttons xdelmsgs" id="xdelmsgs" value="Delete conversation">
                        <input type="button" class="xbuttons xblockuser" id="xblockuser" value="Block user">
                    </div>

                    <script nonce="<?php echo $xnonce; ?>">
                        document.querySelector('.xwelcome').innerText = 'Welcome <?php echo htmlspecialchars(strip_tags($_SESSION['user-username'][0]), ENT_QUOTES, 'UTF-8'); ?> :';

                        function xstartTime(xtime) {
                            var date = new Date(0);
                            date.setSeconds(xtime);
                            var timeString = date.toISOString().substring(11, 19);
                            document.querySelector('.xlogout').innerText = 'Logout ( ' + timeString + ' )';
                            var xmyTimeout = setTimeout(function () {
                                if (xtime > 1) {
                                    xstartTime(--xtime);
                                } else {
                                    clearTimeout(xmyTimeout);
                                    document.querySelector('.xlogout').innerText = 'Login again !';
                                }
                            }, 1000);
                        }
                        xstartTime(<?php echo (86400 - (time() - htmlspecialchars(strip_tags($_SESSION['user-info'][2]), ENT_QUOTES, 'UTF-8'))); ?>);
                    </script>
                    <script src="./functions.js?t=<?php echo filemtime('./functions.js'); ?>" nonce="<?php echo $xnonce; ?>"></script>

                    <?php
                } else {
                    ?>

                    <div class="xlogin">
                        <div class="tab">
                            <button class="tablinks xreg" id="xdefaultopenreg">Register</button>
                            <button class="tablinks xlog" id="xdefaultopenlog">Login</button>
                            <button class="tablinks xdel" id="xdefaultopendel">Delete</button>
                        </div>
                        <div id="xcreateaccount" class="tabcontent">
                            <input type="text" class="xinputs xaddpersonusername" id="xaddpersonusername" maxlength="32" spellcheck="false" placeholder="Username" autocomplete="off">
                            <input type="text" class="xinputs xaddpersonpassword" id="xaddpersonpassword" spellcheck="false" placeholder="Password" autocomplete="off">
                            <button type="button" class="xbuttons xaddpersonbutton" id="xaddpersonbutton"><span class="material-symbols-rounded">person_add</span></button>
                        </div>
                        <div id="xloginaccount" class="tabcontent">
                            <input type="hidden" id="xtokenlogin" value="<?php echo htmlspecialchars(strip_tags($_SESSION['xtokenlogin'][0]), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="text" class="xinputs xloginusername" id="xloginusername" maxlength="32" spellcheck="false" placeholder="Username" autocomplete="off">
                            <input type="text" class="xinputs xloginpassword" id="xloginpassword" spellcheck="false" placeholder="Password" autocomplete="off">
                            <button type="button" class="xbuttons xloginbutton" id="xloginbutton"><span class="material-symbols-rounded">login</span></button>
                        </div>
                        <div id="xdeleteaccount" class="tabcontent">
                            <input type="text" class="xinputs xdeletepersonusername" id="xdeletepersonusername" maxlength="32" spellcheck="false" placeholder="Username" autocomplete="off">
                            <input type="text" class="xinputs xdeletepersonpassword" id="xdeletepersonpassword" spellcheck="false" placeholder="Password" autocomplete="off">
                            <button type="button" class="xbuttons xdeletepersonbutton" id="xdeletepersonbutton"><span class="material-symbols-rounded">person_remove</span></button>
                        </div>
                    </div>

                    <script nonce="<?php echo $xnonce; ?>">
                        const xtab = {
                            xdefaultopenreg: "xcreateaccount",
                            xdefaultopenlog: "xloginaccount",
                            xdefaultopendel: "xdeleteaccount"
                        };
                        Object.keys(xtab).forEach(xid => {
                            document.getElementById(xid).addEventListener("click", event => {
                                opentab(event, xtab[xid]);
                            });
                        });
                        function opentab(evt, tabname) {
                            var i, tabcontent, tablinks;
                            tabcontent = document.getElementsByClassName("tabcontent");
                            for (i = 0; i < tabcontent.length; i++) {
                                tabcontent[i].style.display = "none";
                            }
                            tablinks = document.getElementsByClassName("tablinks");
                            for (i = 0; i < tablinks.length; i++) {
                                tablinks[i].className = tablinks[i].className.replace(" active", "");
                            }
                            document.getElementById(tabname).style.display = "block";
                            evt.currentTarget.className += " active";
                        }
                        document.getElementById("xdefaultopenlog").click();

                        ["xaddpersonusername", "xloginusername", "xdeletepersonusername"].forEach(xid => {
                            document.getElementById(xid).addEventListener("input", function () {
                                xcharactersvalidation(xid);
                            });
                        });

                        function xcharactersvalidation(xcharactersvalidation) {
                            var xcharactersvalidationinput = document.getElementById(xcharactersvalidation).value;
                            if (xcharactersvalidation === 'xaddpersonusername' || xcharactersvalidation === 'xloginusername' || xcharactersvalidation === 'xdeletepersonusername') {
                                if (!/^[a-z0-9]*$/.test(xcharactersvalidationinput)) {
                                    xalertmessage('Allowed characters : 0-9 a-z', "#ffa500");
                                }
                                xcharactersvalidationinput = xcharactersvalidationinput.replace(/[^a-z0-9]/g, '');
                            }
                            document.getElementById(xcharactersvalidation).value = xcharactersvalidationinput;
                        }

                        document.getElementById("xaddpersonbutton").addEventListener("click", function () {
                            const xhttp = new XMLHttpRequest();
                            xhttp.onload = function () {
                                const xobjectvalues = JSON.parse(this.responseText);
                                document.getElementById("xaddpersonusername").value = "";
                                document.getElementById("xaddpersonpassword").value = "";
                                xalertmessage(xobjectvalues.xmessage, xobjectvalues.xtype);
                            };
                            xhttp.onerror = function () {
                                xalertmessage("Unfortunately, there is a problem !", "#ffa500");
                            };
                            xhttp.open("POST", "./messages.php");
                            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                            xhttp.send("xaddperson=xtrue&xaddpersonusername=" + encodeURIComponent(document.getElementById("xaddpersonusername").value) + "&xaddpersonpassword=" + encodeURIComponent(document.getElementById("xaddpersonpassword").value));
                        });

                        document.getElementById("xloginbutton").addEventListener("click", function () {
                            const xhttp = new XMLHttpRequest();
                            xhttp.onload = function () {
                                document.getElementById('xloginusername').value = '';
                                document.getElementById('xloginpassword').value = '';
                                if (this.responseText === '') {
                                    window.location.reload();
                                } else {
                                    const xobjectvalues = JSON.parse(this.responseText);
                                    xalertmessage(xobjectvalues.xmessage, xobjectvalues.xtype);
                                }
                            };
                            xhttp.onerror = function () {
                                xalertmessage("Unfortunately, there is a problem !", "#ffa500");
                            };
                            xhttp.open("POST", "./messages.php");
                            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                            xhttp.send("xloginbutton=xtrue&xloginusername=" + encodeURIComponent(document.getElementById('xloginusername').value) + "&xloginpassword=" + encodeURIComponent(document.getElementById('xloginpassword').value) + "&xtokenlogin=" + encodeURIComponent(document.getElementById('xtokenlogin').value));
                        });

                        document.getElementById("xdeletepersonbutton").addEventListener("click", function () {
                            const xhttp = new XMLHttpRequest();
                            xhttp.onload = function () {
                                const xobjectvalues = JSON.parse(this.responseText);
                                document.getElementById("xdeletepersonusername").value = "";
                                document.getElementById("xdeletepersonpassword").value = "";
                                xalertmessage(xobjectvalues.xmessage, xobjectvalues.xtype);
                            };
                            xhttp.onerror = function () {
                                xalertmessage("Unfortunately, there is a problem !", "#ffa500");
                            };
                            xhttp.open("POST", "./messages.php");
                            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                            xhttp.send("xdeleteperson=xtrue&xdeletepersonusername=" + encodeURIComponent(document.getElementById("xdeletepersonusername").value) + "&xdeletepersonpassword=" + encodeURIComponent(document.getElementById("xdeletepersonpassword").value));
                        });
                    </script>

                    <?php
                }
                ?>

            </div>
        </div>

    </body>
</html>
