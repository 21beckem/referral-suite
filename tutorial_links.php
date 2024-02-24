<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorials</title>
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
    <style>
#allTutorials {
  width: 100%;
  padding: 10px;
}
#allTutorials div {
  border: 1px solid #f2bfff;
  padding: 25px;
  margin-bottom: 15px;
  border-radius: 20px;
  box-shadow: 1px 1px 13px -10px black;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <div id="contactname">Tutorials</div>
      </div>
    </div>
    <div style="height: 80px;"></div>

    <div id="allTutorials"></div>



    <!-- Bottom Nav Bar -->
    <?php
      require_once('make_bottom_nav.php');
      make_bottom_nav(1);
    ?>
    <script>
const allTutorials = _('allTutorials');
const tutorialsObj = <?php echo( readSQL($__MISSIONINFO->mykey, 'SELECT `value` FROM `settings` WHERE `header`="Home Page" AND `name`="tutorials"')[0][0] ); ?>;

for (let k in tutorialsObj) {
  allTutorials.innerHTML += `<div onclick="saveToLinkPagesThenRedirect('`+tutorialsObj[k]+`', this)" href="view_google_slides.php">`+k+`</div>`;
}

    </script>

  </body>
</html>