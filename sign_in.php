<?php
session_start();
unset($_COOKIE['missionInfo']);
setcookie('missionInfo', '', -1, '/');
require_once('sql_tools.php');
if (!empty($_POST)) {
    if (isset($_POST['mission']) || isset($_POST['pass'])) {

        $readRes = readSQL('Referral_Suite_General', 'SELECT * FROM `mission_users` WHERE `name`="'.$_POST['mission'].'"');
        if (count($readRes)==0) {
            $alert = 'Mission not found';
        } else {
            // get that mission's passcode from settings
            $thisMissionPass = readSQL($readRes[0][3], 'SELECT `value` FROM `settings` WHERE `name`="login password"')[0][0];
            if($_POST['pass'] != $thisMissionPass) {
                $alert = 'Password incorrect';
            } else {
                session_start();
                setcookie('missionInfo', json_encode((object) [
                    "name" => $readRes[0][1],
                    "mykey" => $readRes[0][3]
                ]), time() + (86400 * 365), "/"); // 86400 = 1 day
                //var_dump($_COOKIE['missionInfo']);
                header('location: login.php');
            }
        }

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
    <link rel="manifest" href="manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
    <style>
#missionSignIn {
    margin-top: 120px;
    width: 100%;
    padding: 10px;
    position: relative;
}
#missionSignIn select {
    width: 80%;
    padding: 15px;
}
#missionSignIn input {
    width: 80%;
    padding: 10px;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Sign Into Your Mission</a>
      </div>
    </div>
    <div style="height: 35px;"></div>

    <!-- Login Buttons -->
    <form action="sign_in.php" method="POST" id="missionSignIn">
        <center>
            <p style="color: red; height: 30px;"><?php echo($alert) ?></p>
            <select name="mission" id="missionSelect" onchange="this.nextElementSibling.style.display=(this.value=='')?'none':''">
                <option></option>
                <?php
                    $missions = readSQL('Referral_Suite_General', 'SELECT * FROM `mission_users` WHERE 1');
                    foreach ($missions as $i => $row) {
                        echo('<option>'.$row[1].'</option>');
                    }
                ?>
            </select>
            <div id="passBox" style="display: none; margin-top: 70px;">
                <label for="pass">Password:</label>
                <input type="password" name="pass" id="missionPass">
                <input type="submit" class="purpleBtn" style="margin-top: 70px;" value="Sign In">
            </div>
        </center>
    </form>
    <script>

    </script>
  </body>
</html>
