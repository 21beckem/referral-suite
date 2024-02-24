<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Claiming</a>
      </div>
    </div>
    <div style="height: 68px;"></div>

    <!-- Sync and claim area -->
    <div id="claimAsArea" class="w3 container w3-large w3-left-align w3-sync-claim w3-text-white" style="padding-top: 40px;padding-left: 10px;padding-bottom: 40px;"></div>

    <!-- Claim text -->
    <div class="w3-container w3-xlarge w3-center" style="margin-top: 40px;">Are you sure you want to claim this referral?</div>


    <!-- Claim button -->
    <div id="claim" class="w3-container w3-center" style="margin-top:90px">
        <button onclick="claimPerson()" class="w3-button w3-jumbo w3-round-large w3-blue">Claim</button>
    </div>



    <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(4);
    ?>
    <script>
_('claimAsArea').innerHTML = TEAM.name;

function claimPerson() {
  safeRedirect('php_functions/claimReferral.php?perId=' + getCookie('linkPages'));
}
    
    </script>

  </body>
</html>