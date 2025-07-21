
const xvscrollables = document.querySelectorAll('.xverticalscroll');
xvscrollables.forEach(xvscrollable => {
    const xvonDrag = (event) => {
        if (event.pointerType === 'mouse') {
            xvscrollable.scrollTop = xvelementFrom - event.clientY + xvpointerFrom;
        }
    };
    xvscrollable.addEventListener('pointerdown', (event) => {
        if (event.pointerType === 'mouse') {
            xvpointerFrom = event.clientY;
            xvelementFrom = xvscrollable.scrollTop;
            document.addEventListener('pointermove', xvonDrag);
        }
    });
    document.addEventListener('pointerup', (event) => {
        if (event.pointerType === 'mouse') {
            document.removeEventListener('pointermove', xvonDrag);
        }
    });
});

const xhscrollables = document.querySelectorAll('.xhorizontalscroll');
xhscrollables.forEach(xhscrollable => {
    const xhonDrag = (event) => {
        if (event.pointerType === 'mouse') {
            xhscrollable.scrollLeft = xhelementFrom - event.clientX + xhpointerFrom;
        }
    };
    xhscrollable.addEventListener('pointerdown', (event) => {
        if (event.pointerType === 'mouse') {
            xhpointerFrom = event.clientX;
            xhelementFrom = xhscrollable.scrollLeft;
            document.addEventListener('pointermove', xhonDrag);
        }
    });
    document.addEventListener('pointerup', (event) => {
        if (event.pointerType === 'mouse') {
            document.removeEventListener('pointermove', xhonDrag);
        }
    });
});

document.getElementById("xselectimage").addEventListener("click", function () {
    document.getElementById('ximagetoupload').click();
});
document.getElementById("ximagetoupload").addEventListener("change", function (event) {
    document.getElementById('xuploadimage').disabled = false;
    xselectimagefunc(event.target);
});
function xselectimagefunc(xselectfile) {
    var xunits = ['B', 'KB', 'MB', 'GB'];
    var xbytes = xselectfile.files[0].size;
    for (var j = 0; xbytes >= 1024 && j < 4; j++) {
        xbytes /= 1024;
    }
    document.getElementById('xuploadimage').innerText = 'Set : ' + xselectfile.files[0].name + ' [ ' + xbytes.toFixed(2) + ' ' + xunits[j] + ' ]';
}
document.getElementById("xuploadimage").addEventListener("click", function () {
    var file = document.getElementById('ximagetoupload').files[0];
    var formData = new FormData();
    formData.append('xuploadimage', 'xtrue');
    formData.append('ximagetoupload', file);
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        const xobjectvalues = JSON.parse(this.responseText);
        if (xobjectvalues.xtype === '#239f40') {
            document.getElementById('xselectimage').src = xobjectvalues.xmessage;
        } else {
            xalertmessage(xobjectvalues.xmessage, xobjectvalues.xtype);
        }
        document.getElementById('xuploadimage').innerText = 'Choose file';
        document.getElementById('xuploadimage').disabled = true;
    };
    xhttp.onerror = function () {
        xalertmessage("Unfortunately, there is a problem !", "#ffa500");
    };
    xhttp.open("POST", "./messages.php");
    xhttp.send(formData);
});

function xcirclegraph() {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        if (this.responseText) {
            document.querySelectorAll('.xlabels .label').forEach(el => el.remove());

            const xobjectvalues = JSON.parse(this.responseText);
            var xaudioangle = xobjectvalues.xaudioangle;
            var ximageangle = xobjectvalues.ximageangle;
            var xvideoangle = xobjectvalues.xvideoangle;
            var xfreeangle = xobjectvalues.xfreeangle;
            var xcirclegraph = document.querySelector('.xcirclegraph');
            xcirclegraph.style.background = 'conic-gradient(' +
                    '#ff0000 0deg ' + xaudioangle + 'deg,' +
                    '#008000 ' + xaudioangle + 'deg ' + (xaudioangle + ximageangle) + 'deg,' +
                    '#0000ff ' + (xaudioangle + ximageangle) + 'deg ' + (xaudioangle + ximageangle + xvideoangle) + 'deg,' +
                    '#eeeeee ' + (xaudioangle + ximageangle + xvideoangle) + 'deg ' + (xaudioangle + ximageangle + xvideoangle + xfreeangle) + 'deg' +
                    ')';
            var xlabels = [
                {text: '<span class="xred">▪ </span>' + 'audios : ' + xobjectvalues.xaudiosize, angle: xaudioangle / 2},
                {text: '<span class="xgreen">▪ </span>' + 'images : ' + xobjectvalues.ximagesize, angle: xaudioangle + ximageangle / 2},
                {text: '<span class="xblue">▪ </span>' + 'videos : ' + xobjectvalues.xvideosize, angle: xaudioangle + ximageangle + xvideoangle / 2},
                {text: '<span class="xgray">▪ </span>' + 'free-space : ' + xobjectvalues.xfreespace, angle: xaudioangle + ximageangle + xvideoangle + xfreeangle / 2}
            ];
            var xpreviouscoords = [];
            xlabels.forEach(function (xlabel) {
                var angleInRadians = (xlabel.angle - 90) * Math.PI / 180;
                var x = 115 + 89 * Math.cos(angleInRadians);
                var y = 115 + 89 * Math.sin(angleInRadians);
                xpreviouscoords.forEach(function (xcoord) {
                    if (Math.abs(xcoord.y - y) < 20) {
                        y += 20;
                    }
                });
                xpreviouscoords.push({x: x, y: y});
                var xlabeldiv = document.createElement('div');
                xlabeldiv.className = 'label';
                xlabeldiv.innerHTML = xlabel.text;
                xlabeldiv.style.left = x + 'px';
                xlabeldiv.style.top = y + 'px';
                document.querySelector('.xlabels').appendChild(xlabeldiv);
            });
        }
    };
    xhttp.onerror = function () {
        xalertmessage("Unfortunately, there is a problem !", "#ffa500");
    };
    xhttp.open("POST", "./messages.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("xcirclegraph=xtrue");
}

xcirclegraph();

["xreceiver", "xmessage"].forEach(xid => {
    document.getElementById(xid).addEventListener("input", function () {
        xcharactersvalidation(xid);
    });
});

function xcharactersvalidation(xcharactersvalidation) {
    var xcharactersvalidationinput = document.getElementById(xcharactersvalidation).value;
    if (xcharactersvalidation === 'xreceiver') {
        if (!/^[a-z0-9]*$/.test(xcharactersvalidationinput)) {
            xalertmessage('Allowed characters : 0-9 a-z', "#ffa500");
        }
        xcharactersvalidationinput = xcharactersvalidationinput.replace(/[^a-z0-9]/g, '');
    }
    if (xcharactersvalidation === 'xmessage') {
        if (!/^[a-zA-Z0-9!?,. \r\n\p{Emoji_Presentation}]*$/u.test(xcharactersvalidationinput)) {
            xalertmessage('Allowed characters : a-z A-Z 0-9 ! ? , .', "#ffa500");
        }
        xcharactersvalidationinput = xcharactersvalidationinput.replace(/[^a-zA-Z0-9!?,. \r\n\p{Emoji_Presentation}]/gu, '');
    }
    document.getElementById(xcharactersvalidation).value = xcharactersvalidationinput;
}

document.getElementById("xsettingsbtn").addEventListener("click", function () {
    let xsettings = document.getElementById("xsettings");
    let xcurrentdisplay = window.getComputedStyle(xsettings).display;
    if (xcurrentdisplay === "none") {
        xcirclegraph();
        xsettings.style.display = "block";
    } else {
        xsettings.style.display = "none";
    }
});

document.getElementById("xmessage").onkeyup = function () {
    document.getElementById("xlength").innerHTML = (1000 - this.value.length);
};

document.querySelector('.xsubmit').addEventListener("click", function () {
    var xreceiver = document.getElementById('xreceiver').value;
    var xmessage = document.getElementById('xmessage').value;
    var files = document.getElementById('xuploadfiles').files;
    var formData = new FormData();
    formData.append('xsubmitmessages', 'xtrue');
    formData.append('xreceiversubmitmessage', xreceiver);
    formData.append('xmessagesubmitmessage', xmessage);
    for (let i = 0; i < files.length; i++) {
        formData.append('xuploadfiles[]', files[i]);
    }
    if (xreceiver !== '') {
        if (xmessage !== '' || files.length !== 0) {
            if (document.getElementById('xmessage').value.length <= 1000) {
                const xhttp = new XMLHttpRequest();
                xhttp.onload = function () {
                    if (this.responseText !== '') {
                        xalertmessage(this.responseText, "#ffa500");
                    }
                    if (document.getElementById('xreceiver').style.display !== 'none') {
                        document.getElementById('xreceiver').value = '';
                    }
                    document.getElementById('xmessage').value = '';
                    document.getElementById('xlength').innerHTML = '1000';
                    document.getElementById('xemojimessages').style.display = 'none';
                    document.getElementById('xmessages').style.display = 'block';
                    document.getElementById('xuploadfilesbutton').style.color = '#239f40';
                    document.getElementById('xuploadfiles').value = '';
                    xnewlyaddedsmsgs();
                };
                xhttp.onerror = function () {
                    xalertmessage("Unfortunately, there is a problem !", "#ffa500");
                };
                xhttp.open("POST", "./messages.php");
                xhttp.send(formData);
            } else {
                xalertmessage('The number of letters is more than the limit !', "#ffa500");
            }
        } else {
            xalertmessage('There is no text or file to send !', "#ffa500");
        }
    } else {
        xalertmessage('There is no user !', "#ffa500");
    }
});

function xnewlyaddedsmsgs() {
    const xmsgs3 = document.querySelectorAll('.ximsgi');
    xmsgs3.forEach(xmsg3 => {
        xmsg3.addEventListener("contextmenu", function (event) {
            event.preventDefault();
        });
    });
}

function xreceivemessages() {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        document.getElementById("xmessages").innerHTML = this.responseText;
        if (this.responseText !== 'Empty !') {
            document.querySelector('.xnumbermessages').innerText = '[ ' + document.getElementsByClassName("xbfm").length + ' ]';
        } else {
            document.querySelector('.xnumbermessages').innerText = '[ 0 ]';
        }
        xnewlyaddedmsgs();
    };
    xhttp.onerror = function () {
        xalertmessage("Unfortunately, there is a problem !", "#ffa500");
    };
    xhttp.open("POST", "./messages.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("xreceivemessages=xtrue");
}
var xsetintervalreceivemessages = null;
function xreceivemessagessetinterval() {
    document.getElementById('xmessage').value = '';
    document.getElementById('xreceiver').style.display = 'inline-block';
    document.getElementById('xreceiver').value = '';
    document.getElementById('xlength').innerHTML = '1000';
    document.getElementById('xemojimessages').style.display = 'none';
    document.getElementById('xmessages').style.display = 'block';
    document.getElementById('xuploadfilesbutton').style.color = '#239f40';
    document.getElementById('xuploadfiles').value = '';
    xsetintervalreceivemessages = setInterval(function () {
        xreceivemessages();
    }, 1000);
}
xreceivemessagessetinterval();
function xreceivemessagesclearinterval() {
    if (xsetintervalreceivemessages !== null) {
        clearInterval(xsetintervalreceivemessages);
        xsetintervalreceivemessages = null;
    }
}
function xnewlyaddedmsgs() {
    const xmsgs2 = document.querySelectorAll('.xbfm, .xbfmt');
    xmsgs2.forEach(xmsg2 => {
        xmsg2.addEventListener("click", function (event) {
            if (xmsg2.className === 'xbfm') {
                xreceivemessagesclearinterval();
                xreceivemessagesetinterval(xmsg2.getAttribute('datavalue'));
            }
            if (xmsg2.className === 'xbfmt') {
                event.stopPropagation();
            }
        });
    });
}

document.getElementById("xopenoptions").addEventListener("click", function () {
    document.getElementById("xopenoptions").style.display = "none";
    document.getElementById("xactionsbar").style.display = "block";
});

document.getElementById("xcloseoptions").addEventListener("click", function () {
    document.getElementById("xactionsbar").style.display = "none";
    document.getElementById("xdelmsgdate").value = "";
    document.getElementById("xopenoptions").style.display = "inline-block";
});

function xreceivemessage() {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        document.getElementById("xmessages").innerHTML = this.responseText;
        document.getElementById('xdelmsg').style.color = '#239f40';
        xnewlyaddedmsg();
    };
    xhttp.onerror = function () {
        xalertmessage("Unfortunately, there is a problem !", "#ffa500");
    };
    xhttp.open("POST", "./messages.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("xreceivemessage=xtrue&xuserreceivemessage=" + encodeURIComponent(document.getElementById('xreceiver').value));
}
var xsetintervalreceivemessage = null;
function xreceivemessagesetinterval(xreceivemessagesetinterval) {
    if (document.getElementById('xreceiver').style.display !== 'none') {

        document.getElementById('xopenoptions').style.display = 'inline-block';
        document.getElementById('xreceiver').value = xreceivemessagesetinterval;
        document.getElementById('xreceiver').style.display = 'none';
        document.getElementById('xmessage').value = '';
        document.getElementById('xlength').innerHTML = '1000';
        document.getElementById('xemojimessages').style.display = 'none';
        document.getElementById('xmessages').style.display = 'block';
        document.getElementById('xuploadfilesbutton').style.color = '#239f40';
        document.getElementById('xuploadfiles').value = '';
        xsetintervalreceivemessage = setInterval(function () {
            xreceivemessage();
        }, 1000);
    }
}
function xreceivemessageclearinterval() {
    if (xsetintervalreceivemessage !== null) {
        clearInterval(xsetintervalreceivemessage);
        xsetintervalreceivemessage = null;
    }
}
function xnewlyaddedmsg() {
    const xmsgs1 = document.querySelectorAll('.xbfmm');
    xmsgs1.forEach(xmsg1 => {
        xmsg1.addEventListener("click", function () {
            document.getElementById('xdelmsgdate').value = xmsg1.getAttribute('datavalue');
            document.getElementById('xm' + xmsg1.getAttribute('datavalue').replace(/-| |:/g, '')).style.boxShadow = '0px 0px 3px 0px #ffa500, 0px 0px 12px 0px #ffa500 inset';
            document.getElementById('xdelmsg').style.color = '#ffa500';
        });
    });
}

document.getElementById('xemoji').addEventListener("click", function () {
    let xemojimessages = document.getElementById('xemojimessages');
    let xcurrentdisplay = window.getComputedStyle(xemojimessages).display;
    if (xcurrentdisplay === 'none') {
        xemojis();
        document.getElementById('xmessages').style.display = 'none';
        document.getElementById('xemojimessages').style.display = 'block';
    } else {
        document.getElementById('xemojimessages').style.display = 'none';
        document.getElementById('xmessages').style.display = 'block';
    }
});
function xemojis() {
    var xbuttonsemojimessages = '';
    var xemojibuttons = ['231A', '231B', '23E9', '23EA', '23EB', '23EC', '23ED', '23EE', '23EF', '23F0', '23F1', '23F2', '23F3', '23F8', '23F9', '23FA', '2614', '2615', '261D', '2648', '2649', '264A', '264B', '264C', '264D', '264E', '264F', '2650', '2651', '2652', '2653', '267F', '2693', '26A1', '26AA', '26AB', '26BD', '26BE', '26C4', '26C5', '26CE', '26D4', '26EA', '26F2', '26F3', '26F5', '26F9', '26FA', '26FD', '2705', '270A', '270B', '270C', '270D', '2728', '274C', '274E', '2753', '2754', '2755', '2757', '2795', '2796', '2797', '27B0', '27BF', '2B1B', '2B1C', '2B50', '2B55', '1F004', '1F0CF', '1F170', '1F171', '1F17E', '1F17F', '1F18E', '1F191', '1F192', '1F193', '1F194', '1F195', '1F196', '1F197', '1F198', '1F199', '1F19A', '1F201', '1F21A', '1F22F', '1F232', '1F233', '1F234', '1F235', '1F236', '1F238', '1F239', '1F23A', '1F250', '1F251', '1F300', '1F301', '1F302', '1F303', '1F304', '1F305', '1F306', '1F307', '1F308', '1F309', '1F30A', '1F30B', '1F30C', '1F30D', '1F30E', '1F30F', '1F310', '1F311', '1F312', '1F313', '1F314', '1F315', '1F316', '1F317', '1F318', '1F319', '1F31A', '1F31B', '1F31C', '1F31D', '231B', '1F31E', '1F31F', '1F320', '1F32D', '1F32E', '1F32F', '1F330', '1F331', '1F332', '1F333', '1F334', '1F335', '1F337', '1F338', '1F339', '1F33A', '1F33B', '1F33C', '1F33D', '1F33E', '1F33F', '1F340', '1F341', '1F342', '1F343', '1F344', '1F345', '1F346', '1F347', '1F348', '1F349', '1F34A', '1F34B', '1F34C', '1F34D', '1F34E', '1F34F', '1F350', '1F351', '1F352', '1F353', '1F354', '1F355', '1F356', '1F357', '1F358', '1F359', '1F35A', '1F35B', '1F35C', '1F35D', '1F35E', '1F35F', '1F360', '1F361', '1F362', '1F363', '1F364', '1F365', '1F366', '1F367', '1F368', '1F369', '1F36A', '1F36B', '1F36C', '1F36D', '1F36E', '1F36F', '1F370', '1F371', '1F372', '1F373', '1F374', '1F375', '1F376', '1F377', '1F378', '1F379', '1F37A', '1F37B', '1F37C', '1F37E', '1F37F', '1F380', '1F381', '1F382', '1F383', '1F384', '1F385', '1F386', '1F387', '1F388', '1F389', '1F38A', '1F38B', '1F38C', '1F38D', '1F38E', '1F38F', '1F390', '1F391', '1F392', '1F393', '1F3A0', '1F3A1', '1F3A2', '1F3A3', '1F3A4', '1F3A5', '1F3A6', '1F3A7', '1F3A8', '1F3A9', '1F3AA', '1F3AB', '1F3AC', '1F3AD', '1F3AE', '1F3AF', '1F3B0', '1F3B1', '1F3B2', '1F3B3', '1F3B4', '1F3B5', '1F3B6', '1F3B7', '1F3B8', '1F3B9', '1F3BA', '1F3BB', '1F3BC', '1F3BD', '1F3BE', '1F3BF', '1F3C0', '1F3C1', '1F3C2', '1F3C3', '1F3C4', '1F3C5', '1F3C6', '1F3C7', '1F3C8', '1F3C9', '1F3CA', '1F3CB', '1F3CC', '1F3CF', '1F3D0', '1F3D1', '1F3D2', '1F3D3', '1F3E0', '1F3E1', '1F3E2', '1F3E3', '1F3E4', '1F3E5', '1F3E6', '1F3E7', '1F3E8', '1F3E9', '1F3EA', '1F3EB', '1F3EC', '1F3ED', '1F3EE', '1F3EF', '1F3F0', '1F3F4', '1F3F8', '1F3F9', '1F3FA', '1F400', '1F401', '1F402', '1F403', '1F404', '1F405', '1F406', '1F407', '1F408', '1F409', '1F40A', '1F40B', '1F40C', '1F40D', '1F40E', '1F40F', '1F410', '1F411', '1F412', '1F413', '1F414', '1F415', '1F416', '1F417', '1F418', '1F419', '1F41A', '1F41B', '1F41C', '1F41D', '1F41E', '1F41F', '1F420', '1F421', '1F422', '1F423', '1F424', '1F425', '1F426', '1F427', '1F428', '1F429', '1F42A', '1F42B', '1F42C', '1F42D', '1F42E', '1F42F', '1F430', '1F431', '1F432', '1F433', '1F434', '1F435', '1F436', '1F437', '1F438', '1F439', '1F43A', '1F43B', '1F43C', '1F43D', '1F43E', '1F440', '1F442', '1F443', '1F444', '1F445', '1F446', '1F447', '1F448', '1F449', '1F44A', '1F44B', '1F44C', '1F44D', '1F44E', '1F44F', '1F450', '1F451', '1F452', '1F453', '1F454', '1F455', '1F456', '1F457', '1F458', '1F459', '1F45A', '1F45B', '1F45C', '1F45D', '1F45E', '1F45F', '1F460', '1F461', '1F462', '1F463', '1F464', '1F465', '1F466', '1F467', '1F468', '1F469', '1F46A', '1F46B', '1F46C', '1F46D', '1F46E', '1F46F', '1F470', '1F471', '1F472', '1F473', '1F474', '1F475', '1F476', '1F477', '1F478', '1F479', '1F47A', '1F47B', '1F47C', '1F47D', '1F47E', '1F47F', '1F480', '1F481', '1F482', '1F483', '1F484', '1F485', '1F486', '1F487', '1F488', '1F489', '1F48A', '1F48B', '1F48C', '1F48D', '1F48E', '1F48F', '1F490', '1F491', '1F492', '1F493', '1F494', '1F495', '1F496', '1F497', '1F498', '1F499', '1F49A', '1F49B', '1F49C', '1F49D', '1F49E', '1F49F', '1F4A0', '1F4A1', '1F4A2', '1F4A3', '1F4A4', '1F4A5', '1F4A6', '1F4A7', '1F4A8', '1F4A9', '1F4AA', '1F4AB', '1F4AC', '1F4AD', '1F4AE', '1F4AF', '1F4B0', '1F4B1', '1F4B2', '1F4B3', '1F4B4', '1F4B5', '1F4B6', '1F4B7', '1F4B8', '1F4B9', '1F4BA', '1F4BB', '1F4BC', '1F4BD', '1F4BE', '1F4BF', '1F4C0', '1F4C1', '1F4C2', '1F4C3', '1F4C4', '1F4C5', '1F4C6', '1F4C7', '1F4C8', '1F4C9', '1F4CA', '1F4CB', '1F4CC', '1F4CD', '1F4CE', '1F4CF', '1F4D0', '1F4D1', '1F4D2', '1F4D3', '1F4D4', '1F4D5', '1F4D6', '1F4D7', '1F4D8', '1F4D9', '1F4DA', '1F4DB', '1F4DC', '1F4DD', '1F4DE', '1F4DF', '1F4E0', '1F4E1', '1F4E2', '1F4E3', '1F4E4', '1F4E5', '1F4E6', '1F4E7', '1F4E8', '1F4E9', '1F4EA', '1F4EB', '1F4EC', '1F4ED', '1F4EE', '1F4EF', '1F4F0', '1F4F1', '1F4F2', '1F4F3', '1F4F4', '1F4F5', '1F4F6', '1F4F7', '1F4F8', '1F4F9', '1F4FA', '1F4FB', '1F4FC', '1F4FF', '1F500', '1F501', '1F502', '1F503', '1F504', '1F505', '1F506', '1F507', '1F508', '1F509', '1F50A', '1F50B', '1F50C', '1F50D', '1F50E', '1F50F', '1F510', '1F511', '1F512', '1F513', '1F514', '1F515', '1F516', '1F517', '1F518', '1F519', '1F51A', '1F51B', '1F51C', '1F51D', '1F51E', '1F51F', '1F520', '1F521', '1F522', '1F523', '1F524', '1F525', '1F526', '1F527', '1F528', '1F529', '1F52A', '1F52B', '1F52C', '1F52D', '1F52E', '1F52F', '1F530', '1F531', '1F532', '1F533', '1F534', '1F535', '1F536', '1F537', '1F538', '1F539', '1F53A', '1F53B', '1F53C', '1F53D', '1F54B', '1F54C', '1F54D', '1F54E', '1F550', '1F551', '1F552', '1F553', '1F554', '1F555', '1F556', '1F557', '1F558', '1F559', '1F55A', '1F55B', '1F55C', '1F55D', '1F55E', '1F55F', '1F560', '1F561', '1F562', '1F563', '1F564', '1F565', '1F566', '1F567', '1F574', '1F575', '1F57A', '1F590', '1F595', '1F596', '1F5A4', '1F5FB', '1F5FC', '1F5FD', '1F5FF', '1F600', '1F601', '1F602', '1F603', '1F604', '1F605', '1F606', '1F607', '1F608', '1F609', '1F60A', '1F60B', '1F60C', '1F60D', '1F60E', '1F60F', '1F610', '1F611', '1F612', '1F613', '1F614', '1F615', '1F616', '1F617', '1F618', '1F619', '1F61A', '1F61B', '1F61C', '1F61D', '1F61E', '1F61F', '1F620', '1F621', '1F622', '1F623', '1F624', '1F625', '1F626', '1F627', '1F628', '1F629', '1F62A', '1F62B', '1F62C', '1F62D', '1F62E', '1F62F', '1F630', '1F631', '1F632', '1F633', '1F634', '1F635', '1F636', '1F637', '1F638', '1F639', '1F63A', '1F63B', '1F63C', '1F63D', '1F63E', '1F63F', '1F640', '1F641', '1F642', '1F643', '1F644', '1F645', '1F646', '1F647', '1F648', '1F649', '1F64A', '1F64B', '1F64C', '1F64D', '1F64E', '1F64F', '1F680', '1F681', '1F682', '1F683', '1F684', '1F685', '1F686', '1F687', '1F688', '1F689', '1F68A', '1F68B', '1F68C', '1F68D', '1F68E', '1F68F', '1F690', '1F691', '1F692', '1F693', '1F694', '1F695', '1F696', '1F697', '1F698', '1F699', '1F69A', '1F69B', '1F69C', '1F69D', '1F69E', '1F69F', '1F6A0', '1F6A1', '1F6A2', '1F6A3', '1F6A4', '1F6A5', '1F6A6', '1F6A7', '1F6A8', '1F6A9', '1F6AA', '1F6AB', '1F6AC', '1F6AD', '1F6AE', '1F6AF', '1F6B0', '1F6B1', '1F6B2', '1F6B3', '1F6B4', '1F6B5', '1F6B6', '1F6B7', '1F6B8', '1F6B9', '1F6BA', '1F6BB', '1F6BC', '1F6BD', '1F6BE', '1F6BF', '1F6C0', '1F6C1', '1F6C2', '1F6C3', '1F6C4', '1F6C5', '1F6CC', '1F6D0', '1F6D1', '1F6D2', '1F6EB', '1F6EC', '1F6F4', '1F6F5', '1F6F6', '1F6F7', '1F6F8', '1F6F9', '1F6FA', '1F910', '1F911', '1F912', '1F913', '1F914', '1F915', '1F916', '1F917', '1F918', '1F919', '1F91A', '1F91B', '1F91C', '1F91D', '1F91E', '1F91F', '1F920', '1F921', '1F922', '1F923', '1F924', '1F925', '1F926', '1F927', '1F928', '1F929', '1F92A', '1F92B', '1F92C', '1F92D', '1F92E', '1F92F', '1F930', '1F931', '1F932', '1F933', '1F934', '1F935', '1F936', '1F937', '1F938', '1F939', '1F93A', '1F93C', '1F93D', '1F93E', '1F940', '1F941', '1F942', '1F943', '1F944', '1F945', '1F947', '1F948', '1F949', '1F94A', '1F94B', '1F94C', '1F94D', '1F94E', '1F94F', '1F950', '1F951', '1F952', '1F953', '1F954', '1F955', '1F956', '1F957', '1F958', '1F959', '1F95A', '1F95B', '1F95C', '1F95D', '1F95E', '1F95F', '1F960', '1F961', '1F962', '1F963', '1F964', '1F965', '1F966', '1F967', '1F968', '1F969', '1F96A', '1F96B', '1F980', '1F981', '1F982', '1F983', '1F984', '1F985', '1F986', '1F987', '1F988', '1F989', '1F98A', '1F98B', '1F98C', '1F98D', '1F98E', '1F98F', '1F990', '1F991', '1F992', '1F993', '1F994', '1F995', '1F996', '1F997', '1F9C0', '1F9D0', '1F9D1', '1F9D2', '1F9D3', '1F9D4', '1F9D5', '1F9D6', '1F9D7', '1F9D8', '1F9D9', '1F9DA', '1F9DB', '1F9DC', '1F9DD', '1F9DE', '1F9DF', '1F9E0', '1F9E1', '1F9E2', '1F9E3', '1F9E4', '1F9E5', '1F9E6'];
    for (let i = 0; i < 959; i++) {
        xbuttonsemojimessages += '<input type="button" class="xemojibuttons" value="&#x' + xemojibuttons[i] + ';">';
    }
    document.getElementById('xbuttonsemojimessages').innerHTML = xbuttonsemojimessages;
    xemojibtns();
}
function xemojibtns() {
    const xemojibuttons = document.querySelectorAll('#xbuttonsemojimessages .xemojibuttons');
    xemojibuttons.forEach(xemojibutton => {
        xemojibutton.addEventListener("click", function () {
            document.getElementById('xmessage').value += xemojibutton.value;
            document.getElementById('xmessage').focus();
        });
    });
}

document.getElementById("xuploadfilesbutton").addEventListener("click", function () {
    document.getElementById('xuploadfiles').click();
});
document.getElementById("xuploadfiles").addEventListener("change", function (event) {
    xselectfilesfunc(event.target);
});
function xselectfilesfunc(xselectfilesfunc) {
    if (xselectfilesfunc.value !== '') {
        document.getElementById('xuploadfilesbutton').style.color = '#ffa500';
    } else {
        document.getElementById('xuploadfilesbutton').style.color = '#239f40';
    }
}

document.getElementById("xbackmsg").addEventListener("click", function () {
    document.getElementById('xactionsbar').style.display = 'none';
    document.getElementById('xdelmsgdate').value = '';
    xreceivemessageclearinterval();
    xreceivemessagessetinterval();
});

document.getElementById("xdelmsg").addEventListener("click", function () {
    xdelmsg();
});
function xdelmsg() {
    const xhttp = new XMLHttpRequest();
    xhttp.onerror = function () {
        xalertmessage("Unfortunately, there is a problem !", "#ffa500");
    };
    xhttp.open("POST", "./messages.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("xdelmsg=xtrue&xdelmsgdate=" + encodeURIComponent(document.getElementById('xdelmsgdate').value));
}

document.getElementById("xdelmsgs").addEventListener("click", function () {
    xdelmsgs();
});
function xdelmsgs() {
    const xhttp = new XMLHttpRequest();
    xhttp.onerror = function () {
        xalertmessage("Unfortunately, there is a problem !", "#ffa500");
    };
    xhttp.open("POST", "./messages.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("xdelmsgs=xtrue&xdelmsgsusername=" + encodeURIComponent(document.getElementById('xreceiver').value));
}

document.getElementById("xblockuser").addEventListener("click", function () {
    xblockuser();
});
function xblockuser() {
    const xhttp = new XMLHttpRequest();
    xhttp.onerror = function () {
        xalertmessage("Unfortunately, there is a problem !", "#ffa500");
    };
    xhttp.open("POST", "./messages.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("xblockuser=xtrue&xblockuserusername=" + encodeURIComponent(document.getElementById('xreceiver').value));
}

document.querySelector('.xlogout').addEventListener("click", function () {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        window.location.reload();
    };
    xhttp.onerror = function () {
        xalertmessage("Unfortunately, there is a problem !", "#ffa500");
    };
    xhttp.open("POST", "./messages.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("xlogout=xtrue");
});

window.addEventListener('load', function () {
    function xstatus() {
        if (navigator.onLine) {
            xalertmessage("online !", "#239f40");
        } else {
            xalertmessage("offline !", "#ffa500");
        }
    }
    window.addEventListener('online', xstatus);
    window.addEventListener('offline', xstatus);
});
