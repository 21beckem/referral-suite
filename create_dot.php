<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Info</title>
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
#FH_message {
  width: calc(100% - 20px);
  margin: 10px;
  white-space: pre-line;
  box-shadow: 1px 1px 7px -3px black;
  padding: 8px;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <div id="contactname">Create Dot</div>
      </div>
    </div>
    <div style="height: 80px;"></div>

    <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
      <div class="w3-left-align w3-small w3-opacity">Create new Person in Area Book. Name:</div>
      <div id="personName" class="w3-left-align w3-large"></div>
    </div>
    <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Email</div>
        <div id="email" class="w3-left-align w3-large"></div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Address</div>
        <div id="address" class="w3-left-align w3-large"></div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Finding Source</div>
        <div id="findingSource" class="w3-left-align w3-large">Missionary → Family History</div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Preferred Language</div>
        <div id="FH_lang" class="w3-left-align w3-large"></div>
    </div>
    <br>
    <center><h4>Click ✓, then click Send</h4></center>
    <br>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Sending Option</div>
        <div class="w3-left-align w3-large">REFER</div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Follow After Send</div>
        <div class="w3-left-align w3-large">NO</div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Note</div>
        <div id="FH_message"></div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Reason</div>
        <div class="w3-left-align w3-large">Unsure or other</div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Send Method</div>
        <div class="w3-left-align w3-large">Location - Select an Area</div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Location - Select an Area</div>
        <div class="w3-left-align w3-large">Choose teaching area then click SELECT</div>
    </div>
    <br>
    <center>
      <h4>Click ✓, then click SEND</h4>
      <br>
      <div class="w3-container w3-cell w3-center">
        <a class="w3-button w3-xlarge w3-round-large w3-blue" href="referral_send.php">Done. Continue to send</a>            
      </div>    
    </center>



   <!-- Bottom Nav Bar -->
   <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(3);
    ?>
   <script>
(async () => {
  const person = await idToReferral(getCookieJSON('linkPages'));
  _('personName').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
  //_('contactname').innerHTML = _('personName').innerHTML;
  _('email').innerHTML = person[TableColumns['email']];
  _('findingSource').innerHTML = CONFIG['Dot Creation']['finding source'];
  _('address').innerHTML = person[TableColumns['city']] + ' ' + person[TableColumns['zip']];
  _('FH_lang').innerHTML = CONFIG['Dot Creation']['most common language in mission'];
  _('FH_message').innerHTML = CONFIG['Dot Creation']['message'];
})();

   </script>

  </body>
</html>