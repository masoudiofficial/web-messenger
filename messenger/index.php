<?php
require_once './config.php';
$_SESSION['user-username'][6] = 'index.php';
xgeneratecsrftoken();
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
    <head>
        <title>Messenger</title>
        <meta name="title" content="Messenger">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="index, follow">
        <meta name="description" content="This project was developed by @masoudiofficial, and all the code in the script.php file is the result of his ideas and creativity.">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20,400,0,0"/>
        <link rel="stylesheet" type="text/css" href="./common/src/style.css?t=<?php echo filemtime('./common/src/style.css'); ?>">
        <link rel="icon" type="image/png" href="./common/assets/accountimage.png">
        <link rel="canonical" href="http://localhost/messenger/">
    </head>
    <body>

        <div class="xalertmessage"></div>
        <script nonce="<?php echo $xnonce; ?>">
            function xalertmessage(xmessage, xtype) {
                var xelement = document.querySelector('.xalertmessage');
                xelement.innerText = xmessage;
                xelement.style.background = xtype;
                xelement.style.display = 'block';
                setTimeout(() => {
                    xelement.innerText = '';
                    xelement.style.background = '';
                    xelement.style.display = 'none';
                }, 5000);
            }
        </script>

        <div class="xblock">

            <div class="xpbar">
                <div class="xcbar xmessenger">Messenger</div>
                <div class="xcbar xnumbermessages"></div>
            </div>

            <div class="xscrollbar xboxes">

                <?php
                if (xauthentication()) {
                    ?>

                    <div class="xverticalscroll xsettings" id="xsettings">
                        <div class="xwelcome"></div>
                        <div class="xdivision">
                            <div class="xaccountimage">Image account :</div>
                            <div class="xaccountimagediv"><img class="xselectimage" id="xselectimage"></div>
                            <input type="file" class="ximagetoupload" id="ximagetoupload" accept=".gif, .jpeg, .jpg, .png, .webp">
                            <div class="xallowedformats">allowed formats : gif, jpeg, jpg, png, webp</div>
                        </div>
                        <div class="xdivision">
                            <div class="xstoragespace">Storage space :</div>
                            <div class="xlabels"><div class="xcirclegraph"></div></div>
                        </div>
                        <div class="xinfo">
                            Maximum number of conversations : 100<br>
                            Maximum number of message characters : 1000<br>
                            Maximum number of files sent : 10<br>
                            Maximum size of each file : 10 MB<br>
                            Maximum total size of files sent : 100 MB
                        </div>
                        <p class="xlogout"></p>
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
                        document.querySelector('.xwelcome').innerText = 'Welcome <?php echo $_SESSION['user-username'][0]; ?> :';

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
                        xstartTime(<?php echo (86400 - (time() - $_SESSION['user-info'][2])); ?>);
                    </script>
                    <script src="./common/src/script.js?t=<?php echo filemtime('./common/src/script.js'); ?>" nonce="<?php echo $xnonce; ?>"></script>

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
                            <input type="hidden" id="xtokenlogin" value="<?php echo $_SESSION['xtokenlogin'][0]; ?>">
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

                        async function xaddpersonbutton() {
                            const xaddpersonusername = encodeURIComponent(document.getElementById("xaddpersonusername").value);
                            const xaddpersonpassword = encodeURIComponent(document.getElementById("xaddpersonpassword").value);
                            const formData = 'xaddperson=xtrue&xaddpersonusername=' + xaddpersonusername + '&xaddpersonpassword=' + xaddpersonpassword;
                            try {
                                const response = await fetch('./common/modules/isud.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: formData
                                });
                                if (!response.ok) {
                                    throw new Error();
                                }
                                const responseJson = await response.json();
                                if (responseJson) {
                                    document.getElementById("xaddpersonusername").value = "";
                                    document.getElementById("xaddpersonpassword").value = "";
                                    xalertmessage(responseJson.xmessage, responseJson.xtype);
                                    setTimeout(() => { window.location.reload(); }, 1000);
                                }
                            } catch (error) {
                                xalertmessage("Unfortunately, there is a problem !", "#ffa500");
                            }
                        }
                        document.getElementById("xaddpersonbutton").addEventListener("click", xaddpersonbutton);

                        async function xloginbutton() {
                            const formData = new URLSearchParams();
                            formData.append('xloginbutton', 'xtrue');
                            formData.append('xloginusername', document.getElementById('xloginusername').value);
                            formData.append('xloginpassword', document.getElementById('xloginpassword').value);
                            formData.append('xtokenlogin', document.getElementById('xtokenlogin').value);
                            try {
                                const response = await fetch("./common/modules/isud.php", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: formData.toString()
                                });
                                if (!response.ok) {
                                    throw new Error();
                                }
                                const responseJson = await response.json();
                                if (responseJson) {
                                    document.getElementById('xloginusername').value = '';
                                    document.getElementById('xloginpassword').value = '';
                                    if (responseJson.xmessage === 'Login !') {
                                        window.location.reload();
                                    } else {
                                        xalertmessage(responseJson.xmessage, responseJson.xtype);
                                    }
                                }
                            } catch (error) {
                                xalertmessage("Unfortunately, there is a problem !", "#ffa500");
                            }
                        }
                        document.getElementById("xloginbutton").addEventListener("click", xloginbutton);

                        async function xdeletepersonbutton() {
                            const formData = new FormData();
                            formData.append('xdeleteperson', 'xtrue');
                            formData.append('xdeletepersonusername', document.getElementById("xdeletepersonusername").value);
                            formData.append('xdeletepersonpassword', document.getElementById("xdeletepersonpassword").value);
                            try {
                                const response = await fetch("./common/modules/isud.php", {
                                    method: 'POST',
                                    body: formData
                                });
                                if (!response.ok) {
                                    throw new Error();
                                }
                                const responseJson = await response.json();
                                if (responseJson) {
                                    document.getElementById("xdeletepersonusername").value = "";
                                    document.getElementById("xdeletepersonpassword").value = "";
                                    xalertmessage(responseJson.xmessage, responseJson.xtype);
                                }
                            } catch (error) {
                                xalertmessage("Unfortunately, there is a problem !", "#ffa500");
                            }
                        }
                        document.getElementById("xdeletepersonbutton").addEventListener("click", xdeletepersonbutton);
                    </script>

                    <?php
                }
                ?>

            </div>
        </div>

    </body>
</html>
