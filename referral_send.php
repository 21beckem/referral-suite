<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Updates</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
    <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <script src="fox.js"></script>
    <script src="https://21beckem.github.io/SheetMap/sheetmap.js"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/referral-suite/manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Sending Referral</a>
      </div>
    </div>
    <div style="height: 80px;"></div>



  <div class="w3-center" style="padding-top: 100px;">Which area is this person being sent to?</div>
  <div style="height:20px"></div>
  <div class="w3-center">  
    <select onchange="areaOptionChanged(this)" id="areadropdown">
      <option value=""></option>
      <option value="">Error occured while getting area list</option>
    </select>
  </div>

  <div id="Sendbutton" class="w3-container w3-center" style="margin-top:110px; display: none;">
    <button onclick="sendToAnotherArea()" href="index.html" class="w3-button w3-xlarge w3-round-large w3-blue" style="width: 40%;">Send</button>
  </div>

    <!-- Bottom Nav Bar -->
  <?php
  require_once('make_bottom_nav.php');
  make_bottom_nav(3);
  ?>
  <script>

function areaOptionChanged(el) {
  document.getElementById("Sendbutton").style.display = (el.value == "") ? "none" : "";
}
const areas = getCookieJSON('missionAreasList') || null;
if (areas != null) {
  _('areadropdown').innerHTML = '<option></option>';
  for (let i = 0; i < areas.length; i++) {
    _('areadropdown').innerHTML += '<option>' + areas[i][0] + '</option>';
  }
}
  </script>

  </body>
</html>
