<?php
require_once('require_area.php');
$missionInfo = json_decode($_COOKIE['missionInfo']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pin Login</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <link rel="icon" type="image/png" href="/referral-suite/logo.png" />
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
    <style>
#pinPad {
    padding-top: 70px;
    width: 70%;
}
#pinPad td {
    width: 33%;
    text-align: center;
    padding: 15px;
    cursor: pointer;
    font-size: 25px;
}
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
        <div>
            <a>Verify Pin</a>
        </div>
    </div>
    <div style="height: 80px;"></div>



    <div class="w3-center" style="padding-top: 30px;">Enter a PIN to continue</div>
    <div style="height:20px"></div>
    <center>
        <table id="pin-code">
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table id="pinPad">
            <tr>
                <td onclick="addNumber('1')">1</td>
                <td onclick="addNumber('2')">2</td>
                <td onclick="addNumber('3')">3</td>
            </tr>
            <tr>
                <td onclick="addNumber('4')">4</td>
                <td onclick="addNumber('5')">5</td>
                <td onclick="addNumber('6')">6</td>
            </tr>
            <tr>
                <td onclick="addNumber('7')">7</td>
                <td onclick="addNumber('8')">8</td>
                <td onclick="addNumber('9')">9</td>
            </tr>
            <tr>
                <td></td>
                <td onclick="addNumber('0')">0</td>
                <td onclick="addNumber(null)"><i class="fa-solid fa-delete-left"></i></td>
            </tr>
        </table>
    </center>

    <script>
const randomStringFromServer = 'hi';
async function attemptBio() {
    const publicKeyCredentialCreationOptions = {
        challenge: Uint8Array.from(
            randomStringFromServer, c => c.charCodeAt(0)),
        rp: {
            name: "Referral Suite",
            id: window.location.hostname,
        },
        user: {
            id: Uint8Array.from(
                "UZSL85T9AFC", c => c.charCodeAt(0)),
            name: "<?php echo($__TEAM->name . ' | ' . $missionInfo->name) ?>",
            displayName: "<?php echo($__TEAM->name . ' | ' . $missionInfo->name) ?>",
        },
        pubKeyCredParams: [{alg: -7, type: "public-key"}],
        authenticatorSelection: {
            authenticatorAttachment: "cross-platform",
        },
        timeout: 60000,
        attestation: "direct"
    };
    await navigator.credentials.get({publicKey: {
        challenge: Uint8Array.from(
            'hi', c => c.charCodeAt(0)),
        allowCredentials: [{
            id: Uint8Array.from(
                'hi', c => c.charCodeAt(0)),
            type: 'public-key',
            transports: ['usb', 'ble', 'nfc'],
        }],
        timeout: 60000,
    }});
    // const credential = await navigator.credentials.create({
    //     publicKey: publicKeyCredentialCreationOptions
    // });
    console.log(credential.id);
}
attemptBio();

let currentPin = '';

function addNumber(num) {
    if (num == null) {
        currentPin = currentPin.slice(0, -1);
    } else {
        currentPin += num;
    }
    
    if (currentPin.length > 4) {
        currentPin = currentPin.slice(4);
    }
    if (currentPin.length == 4) {
        // test pin, probably by having the server check

        // if (currentPin == CONFIG['General']['pin code']) {
        //     sessionStorage.setItem("logged_in", "1");
        //     safeRedirect('index.html');
        // } else {
        //     _('pin-code').classList.add('shake');
        //     setTimeout(function(el) {
        //         _('pin-code').classList.remove('shake');
        //     }, 300);
        // }
    }
    _('pin-code').innerHTML = '<tr>' + Array(currentPin.length).fill('<td>•</td>').join('') + Array(4 - currentPin.length).fill('<td></td>').join('') + '</tr>';
}

    </script>

</body>

</html>