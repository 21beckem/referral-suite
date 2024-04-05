<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Suite</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
  </head>
  <body style="overflow:hidden">
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <center><div>Referral Suite</div></center>
      </div>
    </div>
    <div style="height: 68px;"></div>

    <div id="loadingAnim" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
      <div class="lds-dual-ring"></div>
    </div>
    <iframe id="google_slides_import" loading="lazy" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>
    <!-- black box -->
    <div style="
      position: absolute;
      bottom: 0;
      width: 100%;
      left: 0;
      height: 76px;
      background-color: black;
      z-index: 3;
    "></div>
    
    <!-- Bottom Nav Bar -->
    <?php
      require_once('make_bottom_nav.php');
      make_bottom_nav(0, '0px');
    ?>
    <script>
setTimeout(() => {
  let link = getCookieJSON('linkPages');
  if (link.includes('www.canva.com')) {
      link = link.substr(0, link.lastIndexOf("/")) + '/view?embed';
  } else {
      link = link.substr(0, link.lastIndexOf("/")) + '/embed';
  }
  _('google_slides_import').src = link;
}, 5);
    </script>

  </body>
</html>
