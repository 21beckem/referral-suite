<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Updates</title>
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
    <link rel="manifest" href="/referral-suite/manifest.webmanifest">
    <script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.7/dist/autoComplete.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.7/dist/css/autoComplete.min.css">
    <meta name="theme-color" content="#462c6a">
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Sending Referral</a>
      </div>
    </div>
    <div style="height: 80px;"></div>



  <div class="w3-center" style="padding-top: 100px;">Which area is this person being sent to?</div>
  <div style="height:20px"></div>
  <div class="w3-center">  
    <input type="text" id="areaInput">
    <br>
    <br>
    <br>
    <h3 id="selectedArea"></h3>
  </div>

  <div id="Sendbutton" class="w3-container w3-center" style="margin-top:90px; display: none;">
    <button onclick="confirmSendReferral()" class="w3-button w3-xlarge w3-round-large w3-blue" style="width: 40%;">Send</button>
  </div>

    <!-- Bottom Nav Bar -->
  <?php
  require_once('make_bottom_nav.php');
  make_bottom_nav(3);
  ?>
  <script>
const areas = <?php echo(json_encode( readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `mission_areas` ORDER BY `mission_areas`.`name` ASC') )) ?>;
const autoCompleteJS = new autoComplete({
  selector: "#areaInput",
  placeHolder: "Search for area...",
  data: {
    src: areas.map(x => x[1]),
    cache: true,
  },
  resultItem: {
    highlight: true
  },
  events: {
    input: {
      selection: (event) => {
        const selection = event.detail.selection.value;
        // autoCompleteJS.input.value = selection;
        _('selectedArea').innerHTML = selection;
        _("Sendbutton").style.display = (selection == "") ? "none" : "";
      }
    }
  }
});

function confirmSendReferral() {
  JSAlert.confirm('Are you sure you want to send this person to ' + _('selectedArea').innerText + '?'+PMGappReminder('send'), '', JSAlert.Icons.Warning).then(res => {
    if (res) {
      sendToAnotherArea();
    }
  });
}
async function sendToAnotherArea() {
  let person = idToReferral(getCookieJSON('linkPages'));
  if (person == null) {
    JSAlert.alert('something went wrong. Try again');
    safeRedirect('index.html');
  }
  const newArea = _('selectedArea').innerText;

  // set new area in data and save to cookie
  person[TableColumns['sent status']] = 'Sent';
  person[TableColumns['teaching area']] = newArea;

  // follow up
  let nextFU = new Date();
  person[TableColumns['sent date']] = nextFU.toISOString().slice(0, 19).replace('T', ' ');

  nextFU.setDate(nextFU.getDate() + parseInt(CONFIG['Follow Ups']['initial delay after sent']));
  nextFU.setHours(3, 0, 0, 0);
  person[TableColumns['next follow up']] = nextFU.toISOString().slice(0, 19).replace('T', ' ');

  if (await savePerson(person)) {
    alert('Sent!');
    safeRedirect('index.php');

    //givePoints
    // setAddFoxPoints(10);               < - - come back to this later!
  }
}
  </script>

  </body>
</html>
