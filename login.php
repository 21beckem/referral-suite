<?php
  session_start();
  setcookie('teamId', '', -1, '/');
  setcookie('__TEAM', '', -1, '/');
  setcookie('__CONFIG', '', -1, '/');
  if (!isset($_COOKIE['missionInfo'])) {
    header('location: sign_in.php');
  }
  $missionInfo = json_decode($_COOKIE['missionInfo']);
  require_once('sql_tools.php');

  if (!empty($_POST)) {
    if (isset($_POST['teamId'])) {
      setcookie('teamId', $_POST['teamId'], time() + (10 * 365 * 24 * 60 * 60), '/');
      header('location: index.php');
    }
  }
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
    <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
    <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <script src="https://21beckem.github.io/SheetMap/sheetmap.js"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/referral-suite/manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
    <style>
#signOutBtn {
  border: none;
  color: red;
  border-radius: 4px;
  padding: 4px 20px;
  margin: 10px;
  background-color: #f9f9f9;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Choose your area</a>
      </div>
    </div>
    <div style="height: 35px;"></div>

    <!-- Login Buttons -->
    <div id="arealoginbuttons">
    <?php
      $areas = readSQL($missionInfo->mykey, 'SELECT * FROM `teams` WHERE 1');
      foreach ($areas as $i => $row) {
        echo('<button onclick="signInAs('.$row[0].')">'.$row[1].'</button>');
      }
    ?>
    </div>
    <form action="login.php" method="POST" id="subForm">
      <input type="hidden" name="teamId" id="teamId">
    </form>
    <button id="signOutBtn" onclick="signOutOfMission()">Sign out of <?php echo($missionInfo->name) ?> Mission</button>
    <script>
let subForm = document.getElementById('subForm');
let teamId = document.getElementById('teamId');
function signInAs(x) {
  teamId.value = x;
  subForm.submit();
}
function signOutOfMission() {
  JSAlert.confirm('Are you sure you want to sign out of the <?php echo($missionInfo->name) ?> Mission?', '', JSAlert.Icons.Warning).then(res => {
    if (res) {
      window.location.href = 'sign_in.php';
    }
  });
}
    </script>
  </body>
</html>
