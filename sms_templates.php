<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SMS Templates</title>
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
    <meta name="theme-color" content="#462c6a">
        <style>
#selectRefType {
    width: 100%;
    padding: 5px;
    border-radius: 3px;
}
#messageExamples {
	width: 100%;
	padding: 0px 10px;
}
.googleMessage {
    width: 100%;
    background-color: rgb(111, 221, 255);
    padding: 10px 15px;
    border-radius: 20px;
	  white-space: pre-line;
    color: var(--black);
}
@media (prefers-color-scheme: dark) {
  .googleMessage {
    background-color: rgb(23 158 199);
  }
}
.useThisTemplateBtn {
    padding: 5px 20px;
    border-radius: 3px;
    margin-top: 10px;
    border: 0;
    background-color: var(--sms-color);
    color: white;
    width: max-content;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div class="w3-container w3-padding-16">
        <div class="contact_info">Create SMS</div>
      </div>
      <div onclick="openGoogleSlides(CONFIG['tips on prewritten messages page']['SMS'])" class="w3-container w3-cell w3-xlarge w3-right-align">
        
        <i class="fa-solid fa-circle-question w3-text-white" style="position: relative;"></i>
      </div>
    </div>
    <div style="height: 80px;"></div>
    <div class="w3-container w3-white w3-padding w3-card-subtle">
        <a id="startBlankBtn" onclick="logAttemptBeforeSendingToLink(this,1)" href="#" target="_parent" class="w3-button w3-sms-color w3-large w3-round" style="margin-right: 10px;">Start a Blank Message</a>OR
    </div>
    
    <div id="messageExamples">
		<div style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
			<div class="lds-dual-ring"></div>
		</div>
    </div>


    <!-- Bottom Nav Bar -->
  <?php
  require_once('make_bottom_nav.php');
  make_bottom_nav(3);
  ?>
	<script>
setTimeout(async ()=> {
  const messageExamples = _('messageExamples');
  await fillMessageExamples('sms', messageExamples);	
}, 10);
async function logAttemptBeforeSendingToLink(el, type) {
  await logAttempt(type);
  setTimeout(() => {
    safeRedirect('contact_info.php');
  }, 10);
  localStorage.setItem('justAttemptedContact', '1');
}

async function fillMessageExamples(typ, pasteBox) {
  let areaEmail = "<?php echo($__TEAM->email); ?>";
  const person = idToReferral(getCookieJSON('linkPages'));
  let requestType = person[TableColumns['type']];
  const emailLink = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[TableColumns['email']] + '&entry.873933093=' + areaEmail + '&entry.1947536680=';
  const link_beginning = (typ == 'sms') ? ('sms:' + encodeURI(String(person[TableColumns['phone']])) + '?body=') : emailLink;
  const _destination = (typ == 'sms') ? '_parent' : '_blank';
  _('startBlankBtn').href = link_beginning;
  _('startBlankBtn').target = _destination;
  const reqMssgUrl = 'php_functions/templates.php?refTyp=' + encodeURI(requestType) + '&type=' + encodeURI(typ);
  //console.log(reqMssgUrl);
  const rawFetch = await safeFetch(reqMssgUrl);
  let rawJson = await rawFetch.json();
  const Messages = rawJson.map( x => x[3]);
  //console.log(Messages);
  let output = "";
  for (let i = 0; i < Messages.length; i++) {
      output += '<div class="w3-panel w3-card-subtle w3-light-gray w3-padding-16"><div class="googleMessage">' + Messages[i] + '</div><button onclick="sendToCompletionPage(\'' + typ + '\', this)" class="useThisTemplateBtn">Use This Template</button></div>';
  }
  pasteBox.innerHTML = output;
}
function sendToCompletionPage(smsOrEmail, el) {
  setCookie('completeThisMessage', el.previousElementSibling.innerHTML);
  safeRedirect(smsOrEmail + '_completer.php');
}
	</script>

  </body>
</html>