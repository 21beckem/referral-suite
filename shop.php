<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
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
    <link rel="manifest" href="/referral-suite-manager/manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
    <style>
#foxWindow {
  position: fixed;
  background-color: blue;
  width: 100%;
  height: 170px;
  background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(228,227,227,1) 100%);
}
#foxWindow img {
  position: relative;
  width: 170px;
  bottom: -20px;
}
#wholePageCard {
  background-color: var(--white);
  width: calc(100% + 2px);
  position: relative;
  margin-right: -1px;
  margin-left: -1px;
  margin-top: 170px;
  box-shadow: 0px 11px 20px 3px black;
}
@media (prefers-color-scheme: dark) {
  #foxWindow {
    color-scheme: dark;
    background: radial-gradient(circle, rgb(68 68 68) 0%, rgb(38 38 38) 100%);
  }
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Fox Shop</a>
      </div>
    </div>
    <div style="height: 60px;"></div>

    <div id="foxWindow">
      <center><img src="img/fox_profile_pics/<?php echo($__TEAM->color); ?>.svg" alt=""></center>
    </div>

    <div id="wholePageCard" style="height: calc(100vh - 250px)">
      <div style="height: 60px;"></div>
      <center>
        <h2>Coming Soon!</h2>
        <br>
        <h4>Stay tuned for more details :)</h4>
      </center>
    </div>
    <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(5, '0px');
    ?>

  </body>
</html>