<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Call</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />
    <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
    <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <script src="fox.js"></script>
    <script src="https://21beckem.github.io/SheetMap/sheetmap.js"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/referral-suite/manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
    <style>
#toCallBtn {
  position: absolute;
  width: 100%;
  left: 0;
  bottom: 59px;
  z-index: 1;
  box-shadow: 1px 2px 15px -7px black;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <center><div>How to Call</div></center>
      </div>
    </div>
    <div style="height: 68px;"></div>

    <div id="loadingAnim" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
      <div class="lds-dual-ring"></div>
    </div>
    <iframe id="google_slides_import" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>

    <button id="toCallBtn" class="w3-button w3-xlarge w3-blue" onclick="callThenGoBack()"><i class="fa fa-phone-square w3-xxlarge w3-text-call-color"></i> CONTINUE TO CALL <i class="fa-solid fa-play"></i></button>            

    
    <!-- Bottom Nav Bar -->
  <?php
  require_once('make_bottom_nav.php');
  make_bottom_nav(3);
  ?>
   <script>
const person = idToReferral(getCookieJSON('linkPages'));
let link = CONFIG['tips before calling'][person[TableColumns['type']]];
if (link == undefined) {
  _('loadingAnim').innerHTML = '<center><h3>No Help Presentation</h3><p>Contact your team leader to get one added!</p><center>';
} else {
  if (link.includes('www.canva.com')) {
    link = link.substr(0, link.lastIndexOf("/")) + '/view?embed';
  } else if (link.includes('docs.google.com')) {
    link = link.substr(0, link.lastIndexOf("/")) + '/embed';
  } else {
    console.error('Unrecognized presentation link. Will open in new tab:' + link);
  }
  
  _('google_slides_import').src = link;
}
async function callThenGoBack() {
  const person = idToReferral(getCookieJSON('linkPages'));
  window.open('tel:+' + person[TableColumns['phone']], '_blank');
  if (await logAttempt(0)) {
    localStorage.setItem('justAttemptedContact', '0');
    safeRedirect('contact_info.php');
  }
}

   </script>

  </body>
</html>
