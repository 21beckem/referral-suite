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
    <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
    <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <script src="fox.js"></script>
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
        <div id="contactname">FH Referral</div>
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
        <div class="w3-left-align w3-large">Missionary → Family History</div>
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
    <center><h4>Click ✓, then click SEND</h4></center>

    <div class="w3-container" style="margin-top: 30px">
      <div class="w3-container w3-cell w3-center">
        <a class="w3-button w3-xlarge w3-round-large w3-blue" href="referral_send.php">Done?</a>            
      </div>
    </div>
    




   <!-- Bottom Nav Bar -->
   <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(3);
    ?>
   <script>

const person = idToReferral(getCookieJSON('linkPages'));
_('personName').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
//_('contactname').innerHTML = _('personName').innerHTML;
_('email').innerHTML = person[TableColumns['email']];
_('address').innerHTML = person[TableColumns['city']] + ' ' + person[TableColumns['zip']];
_('FH_lang').innerHTML = CONFIG['General']['most common language in mission'];
_('FH_message').innerHTML = makeFHMessage(person);
function makeFHMessage(per) {
  if (per[TableColumns['referral origin']].toLowerCase().includes('fb') || per[TableColumns['referral origin']].toLowerCase().includes('ig')) {
    return `This is a FAMILY HISTORY REFERRAL from Facebook!! This person clicked on a FB ad and wants help with Family History! Contact them as as soon as possible. USE EMAIL!

GOOD LUCK!

What they want help with: ` + per[TableColumns['help request']] + `

How experienced they are: ` + per[TableColumns['experience']];
  } else {
    return `This is a FAMILY HISTORY REFERRAL from the MISSION WEBSITE!! This person went to the website and wants help with Family History! Contact them as as soon as possible. USE EMAIL!

GOOD Luck!

What they want help with: ` + per[TableColumns['help request']] + `

How experienced they are: ` + per[TableColumns['experience']];
  }
}

   </script>

  </body>
</html>