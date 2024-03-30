<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Up Response</title>
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
        <a>How did it go?</a>
      </div>
    </div>
    <div style="height: 80px;"></div>



  <div class="w3-center" style="padding-top: 100px;">How is the referral doing?</div>
  <div style="height:20px"></div>
  <div class="w3-center">  
    <select onchange="areaOptionChanged(this)" id="statusdropdown">
    </select>
  </div>

  <div id="Sendbutton" class="w3-container w3-center" style="margin-top:110px; display: none;">
    <button onclick="saveFollowUpForm()" href="index.html" class="w3-button w3-xlarge w3-round-large w3-blue" style="width: 40%;">Save</button>
  </div>

    <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(3);
    ?>

  <script>
async function saveFollowUpForm() {
  let person = await idToReferral(getCookie('linkPages'));
  if (person == null) {
    JSAlert.alert('something went wrong. Try again');
    safeRedirect('index.php');
  }
  const status = document.getElementById('statusdropdown').value;

  let clickedOption = Object.keys(CONFIG['Follow Ups']['status delays'])[parseInt(status)];
  let delay = CONFIG['Follow Ups']['status delays'][clickedOption];

  if (isNaN(parseInt(delay))) {
    person[TableColumns['AB status']] = delay;
    person[TableColumns['next follow up']] = 'MAKE_VALUE_NULL';
  } else {
    delay = parseInt(delay);
    let nextFU = new Date();
    nextFU.setHours(3, 0, 0, 0);
    nextFU.setDate(nextFU.getDate() + delay);
    person[TableColumns['next follow up']] = nextFU.toISOString().slice(0, 19).replace('T', ' ');
  }

  person[TableColumns['follow up status']] = status;
  person[TableColumns['amount of times followed up']] = parseInt(person[TableColumns['amount of times followed up']]) + 1;

  // alert(JSON.stringify( parseInt(status) ));
  let statusText = Object.keys(CONFIG['Follow Ups']['status delays'])[parseInt(status)];
  if (await savePerson(person, 'follow up', statusText)) {
    JSAlert.alert('Saved!', '', JSAlert.Icons.Success).then(()=> {
      safeRedirect('index.php');
    });
  }
}
function fillInFollowUpOptions(el) {
  let out = "<option></option>";
  for (let i = 0; i < Object.keys(CONFIG['Follow Ups']['status delays']).length; i++) {
    out += '<option value="' + i + '">' + Object.keys(CONFIG['Follow Ups']['status delays'])[i] + '</option>';
  }
  el.innerHTML = out;
}
fillInFollowUpOptions(_('statusdropdown'));

function areaOptionChanged(el) {
  _("Sendbutton").style.display = (el.value == "") ? "none" : "";
}

  </script>

  </body>
</html>
