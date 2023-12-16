<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unclaimed Referrals</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
  <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
  <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
  <script src="jsalert.js"></script>
  <script src="everyPageFunctions.php"></script>
  <script src="fox.js"></script>
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="manifest" href="manifest.webmanifest">
  <meta name="theme-color" content="#462c6a">
  <style>
#emailBodyInput {
  width: calc(100% - 20px);
  background-color: var(--white);
  padding: 5px;
  border-radius: 5px;
  min-height: 33px;
  max-height: 325px;
  overflow-y: scroll;
}
#emailSendBtn {
  width: 33px;
  height: 33px;
  background-color: transparent;
  border: none;
  line-height: normal;
  font-size: 25px;
  transform: translate(-10px,-2px);
}
#allMessages .message.us {
  float: right;
  box-shadow: 0 0 13px -9px red inset;
}
#allMessages .message {
  box-shadow: 0 0 13px -9px var(--mainColor) inset;
  float:left;
  border: 1px solid rgba(118, 118, 118, 0.3);
  background-color: rgba(239, 239, 239, 0.3);
  width: calc(100% - 20px);
  padding: 10px;
  margin-bottom: 15px;
  min-height: 45px;
  border-radius: 5px;
}
  </style>
</head>

<body>
  <!-- Top Bar -->
  <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
    <div onclick="safeRedirect('contact_info.php')" class="w3-container w3-cell w3-xlarge w3-left-align">
      <i class="fa-solid fa-angle-left w3-text-white" style="position: relative;"></i>
    </div>
    <div class="w3-container w3-padding-16" style="text-align:center">
      <div class="contact_info" id="contactname"></div>
    </div>
    <div class="w3-container w3-cell w3-xlarge w3-right-align"></div>
  </div>
  <div style="height: 80px;"></div>

  <!-- All messages -->
  <div class="w3-container">
    <div id="allMessages">
      <div class="message us">Jag heter {Your Name}. Jag är representant med webbsidan VandraiTro.se Jag fick din information för att du svarade på en annons att du vill få en besök av några av våra</div>
      <div class="message">Ja</div>
      <div class="message us">Jag heter {Your Name}. Jag är representant med webbsidan VandraiTro.se Jag fick din information för att du svarade på en annons att du vill få en besök av några av våra</div>
      <div class="message">Faktist: nej.</div>
      <div class="message us">BRUH!!!!!!! Jag heter {Your Name}. Jag är representant med webbsidan VandraiTro.se Jag fick din information för att du svarade på en annons att du vill få en besök av några av våra</div>
      <div class="message">Eller... Faktist...</div>
      <div class="message us">Jag heter {Your Name}. Jag är representant med webbsidan VandraiTro.se Jag fick din information för att du svarade på en annons att du vill få en besök av några av våra</div>
    </div>
  </div>

  <!-- Bottom Nav Bar -->
  <div id="bottomNavSpacer" style="height: 61px;"></div>
  <div style="width:100%; display: flex; align-items:flex-end; padding-left:10px; min-height: unset;" id="BottomNavBar">
    <div style="flex:1">
      <div contenteditable id="emailBodyInput"></div>
    </div>
    <div>
      <button id="emailSendBtn"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
  </div>
  <script>

const person = idToReferral(getCookie('linkPages'));
_('contactname').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
const emailBodyInput = _('emailBodyInput');

emailBodyInput.addEventListener("input", (e) => {
  //console.log(e);
  _('bottomNavSpacer').style.height = (_('BottomNavBar').scrollHeight + 5) + 'px';
}, false);
  </script>
</body>
</html>