<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Disinterest</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Confirm Disinterest</a>
      </div>
    </div>
    <div style="height: 80px;"></div>



  <div class="w3-center" style="padding-top: 100px;">Why are they not interested?</div>
  <div style="height:20px"></div>
  <div class="w3-center">  
    <select onchange="areaOptionChanged(this)" id="deceaseDropdown"></select>
  </div>

  <div id="Sendbutton" class="w3-container w3-center" style="margin-top:110px; display: none;">
    <button onclick="confirmDeceasePerson()" href="index.html" class="w3-button w3-xlarge w3-round-large w3-blue">Confirm</button>
  </div>

    <!-- Bottom Nav Bar -->
  <?php
  require_once('make_bottom_nav.php');
  make_bottom_nav(3);
  ?>
  <script>
function fillInDeceeaseReasons() {
  let out = "<option></option>";
  for (let i = 0; i < Object.keys(CONFIG['Stop Contacting']['reasons']).length; i++) {
    out += '<option value="' + Object.keys(CONFIG['Stop Contacting']['reasons'])[i] + '">' + CONFIG['Stop Contacting']['reasons'][Object.keys(CONFIG['Stop Contacting']['reasons'])[i]] + '</option>';
  }
  _('deceaseDropdown').innerHTML = out;
}
function areaOptionChanged(el) {
  document.getElementById("Sendbutton").style.display = (el.value == "") ? "none" : "";
}
async function confirmDeceasePerson() {
  JSAlert.confirm('Are you sure you want to confirm this referral as Not Interested?'+(await PMGappReminder()), '', JSAlert.Icons.Warning).then(res => {
    if (res) {
      deceasePerson();
    }
  });
}
async function deceasePerson() {
  let person = await idToReferral(getCookieJSON('linkPages'));
  if (person == null) {
    JSAlert.alert('Something went wrong. Try again');
    safeRedirect('index.php');
  }

  // set new area in data and save to cookie
  person[TableColumns['sent status']] = 'Not interested';
  person[TableColumns['AB status']] = 'Grey';
  person[TableColumns['not interested reason']] = _('deceaseDropdown').value;

  let reasonText = CONFIG['Stop Contacting']['reasons'][_('deceaseDropdown').value];

  if (await savePerson(person, 'marked as NI', reasonText)) {
    JSAlert.alert('Marked as not interested!', '', JSAlert.Icons.Success).then(()=> {
      safeRedirect('index.php');
    });
  }
}

fillInDeceeaseReasons();
  </script>

  </body>
</html>
