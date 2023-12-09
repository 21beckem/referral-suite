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
</head>

<body>
  <!-- Top Bar -->
  <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
    <div onclick="safeRedirect('contact_info.php')" class="w3-container w3-cell w3-xlarge w3-left-align">
      <i class="fa-solid fa-angle-left w3-text-white" style="position: relative;"></i>
    </div>
    <div class="w3-container w3-padding-16">
      <div class="contact_info" id="contactname"></div>
    </div>
  </div>
  <div style="height: 80px;"></div>

  <!-- List of items -->
  <div class="w3-container">
    <div id="unclaimedlist"></div>
  </div>

  <!-- Bottom Nav Bar -->
  <div style="height: 80px;"></div>
  <div style="width:100%; display: flex; align-items:flex-start; padding: 10px" id="BottomNavBar">
    <div style="flex:1">
      <textarea style="width:calc(100% - 20px)" name="" id=""></textarea>
    </div>
    <div>
      <button><i class="fa-solid fa-paper-plane"></i></button>
    </div>
  </div>
  <script>

const person = idToReferral(getCookie('linkPages'));
_('contactname').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];

  </script>
</body>
</html>