<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
    <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <script src="fox.js"></script>
    <link rel="stylesheet" href="schedule/schedule.css">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/referral-suite/manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue" style="z-index: 99999999;">
      <div>
        <a>Schedule</a>
      </div>
    </div>
    <div style="height: 68px;"></div>
    
    <div id="scheduleParent" style="height: calc(100% - 126px);">
      <div id="timesContainer">
        <div id="timesMarginBox"></div>
      </div>
      <div id="scheduleColDivs">
        <div id="loadingAnim" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
          <div class="lds-dual-ring"></div>
        </div>
      </div>
    </div>

    <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(2, '0px');
    ?>
    <script>
      const schedArr = <?php echo( readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `schedule` WHERE 1')[0][0] ); ?>.transpose();
      const teamInfos = <?php echo(json_encode( readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `teams` WHERE 1') )); ?>;
      const InboxColors = <?php echo(json_encode($InboxColors)); ?>;
    </script>
    <script src="schedule/schedule.js"></script>
  </body>
</html>
