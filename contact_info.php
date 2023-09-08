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
    <script src="https://21beckem.github.io/SheetMap/sheetmap.js"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/referral-suite/manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div class="w3-container w3-padding-16">
        <div class="contact_info" id="contactname"></div>
      </div>
      <div onclick="sendToDeceasePage(this)" href="decease.html" class="w3-container w3-cell w3-xlarge w3-right-align">
        <i class="fa fa-trash-o w3-text-white" style="position: relative;"></i>
      </div>
    </div>
    <div style="height: 80px;"></div>

    <!-- Contact Card -->
    <div class="w3-card">
        <div class="w3-cell-row w3-padding">
            <div class="w3-container w3-center w3-xlarge">Contact Methods</div>
        </div>

        <div class="w3-cell-row w3-padding-16">
            <div class="w3-container w3-cell w3-xxlarge w3-center">
                <a id="telnumber" href="help_before_call.html" target="_parent">
                    <i class="fa fa-phone-square w3-text-call-color">
                      <div class="w3-tiny w3-opacity" style="height: 0;">Call</div>
                    </i>
                </a>
            </div>

            <div class="w3-container w3-cell w3-xxlarge w3-center">
                <a id="smsnumber" href="sms_templates.html">
                    <i class="fa fa-comment w3-text-sms-color">
                      <div class="w3-tiny w3-opacity" style="height: 0;">SMS</div>
                    </i>
                </a>
            </div>

            <div class="w3-container w3-cell w3-xxlarge w3-center">
                <a id="emailcontact" href="email_templates.html">
                    <i class="fa fa-envelope w3-text-email-color">
                      <div class="w3-tiny w3-opacity" style="height: 0;">Email</div>
                    </i>
                </a>
            </div>
        </div>
    </div>
    <div style="height: 10px;"></div>

    <!-- Contact card list -->
    <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
      <div class="w3-left-align w3-small w3-opacity">Attempt Tracker</div>
      <table id="attemptLog" cellspacing=0>
        <colgroup>
          <col>
          <col id="attemptLog_dayIndex0">
          <col id="attemptLog_dayIndex1"> <!--  style="background-color: var(--light-grey);" -->
          <col id="attemptLog_dayIndex2">
          <col id="attemptLog_dayIndex3">
          <col id="attemptLog_dayIndex4">
          <col id="attemptLog_dayIndex5">
        </colgroup>
        <tr id="attemptLog_weekdays">
          <td></td>
          <td>•</td>
          <td>•</td>
          <td>•</td>
          <td>•</td>
          <td>•</td>
          <td>•</td>
          <td>•</td>
        </tr>
        <tr>
          <td><i class="fa fa-phone-square w3-text-call-color w3-xlarge" style="padding-left: 5px;"></i></td>
          <td><div class="attemptDot" id="attemptLogDot_0,0"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_0,1"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_0,2"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_0,3"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_0,4"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_0,5"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_0,6"></div></td>
        </tr>
        <tr>
          <td><i class="fa fa-comment w3-text-sms-color w3-xlarge" style="padding-left: 3px;"></i></td>
          <td><div class="attemptDot" id="attemptLogDot_1,0"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_1,1"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_1,2"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_1,3"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_1,4"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_1,5"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_1,6"></div></td>
        </tr>
        <tr>
          <td><i class="fa fa-envelope w3-text-email-color w3-xlarge" style="padding-left: 3px;"></i></td>
          <td><div class="attemptDot" id="attemptLogDot_2,0"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_2,1"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_2,2"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_2,3"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_2,4"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_2,5"></div></td>
          <td><div class="attemptDot" id="attemptLogDot_2,6"></div></td>
        </tr>
      </table>
    </div>
    <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
      <div class="w3-left-align w3-small w3-opacity">Referral type</div>
      <div id="referraltype" class="w3-left-align w3-large"></div>
    </div>
    <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Referral origin</div>
        <div id="referralorigin" class="w3-left-align w3-large"></div>
    </div>
    <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Phone Number</div>
        <div id="phonenumber" class="w3-left-align w3-large"></div>
    </div>
    <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Email</div>
        <div id="email" class="w3-left-align w3-large"></div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Address</div>
        <div id="address" class="w3-left-align w3-large"></div>
        <div class="w3-container w3-cell w3-right-align">
            <a id="googlemaps" href="http://maps.google.com/?q=your+query" target="_blank">
                <i class="fa fa-map-marker w3-text-dark-gray w3-xlarge" style="margin-top: 5px;"></i>
            </a>
        </div>
    </div>
    <div class="w3-container w3-margin-top w3-margin-bottom w3-border-bottom">
        <div class="w3-left-align w3-small w3-opacity">Preferred Language</div>
        <div id="prefSprak" class="w3-left-align w3-large"></div>
    </div>
    <div class="w3-container w3-cell-row w3-margin-top w3-border-bottom">
      <div class="w3-left-align w3-small w3-opacity">Ad Name</div>
      <div id="adName" class="w3-left-align w3-large"></div>
      <div class="w3-container w3-cell w3-right-align">
          <a id="adDeck" href="" target="_blank">
              <i class="fa fa-external-link w3-text-dark-gray w3-xlarge" style="margin-top: 18px;"></i>
          </a>
      </div>
  </div>
    <div class="w3-container w3-cell-row" style="margin-top: 40px;">
        <button id="sendReferralBtn" class="w3-button w3-xlarge w3-round-large w3-blue" onclick="verifySentInSMOEsAB(this)" href="referral_edit.html">Send referral</button>
    </div>
    




  <!-- Bottom Nav Bar -->
  <?php
  require_once('make_bottom_nav.php');
  make_bottom_nav(3);
  ?>
  <script>
function fillInAttemptLog() {
  let person = idToReferral(getCookie('linkPages'));
  let al = Array(7).fill([0, 0, 0]);
  try {
    al = JSON.parse(person[TableColumns['attempt log']]);
  } catch (e) {
    person[TableColumns['attempt log']] = JSON.stringify(al);
  }

  // make days of the week start on right day
  let startDay = new Date(person[TableColumns['date']]);
  const shorterDays = ['sun', 'mon', 'tue', 'wed', 'thur', 'fri', 'sat', 'sun', 'mon', 'tue', 'wed', 'thur', 'fri', 'sat', 'sun', 'mon'];
  let daysString = '<td></td>';
  for (let i = 0; i < 7; i++) {
    daysString += '<td>' + shorterDays[startDay.getDay() + i] + '</td>';
  }
  _('attemptLog_weekdays').innerHTML = daysString;

  //set dot colors
  for (let i = 0; i < al.length; i++) {
    for (let j = 0; j < al[i].length; j++) {
      if (al[i][j] == 1) {
        _('attemptLogDot_' + j + ',' + i).classList.add('contactDotBeenAttempted');
      }
    }
  }

  //highlight todays thing
  try {
    let todaysI = getTodaysInxdexOfAttempts(person);
    _('attemptLog_dayIndex' + todaysI).style.backgroundColor = 'var(--light-grey)';
    for (let i = 0; i < 7; i++) {
      _('attemptLogDot_0,' + i).disabled = (i!=todaysI);
      _('attemptLogDot_1,' + i).disabled = (i!=todaysI);
      _('attemptLogDot_2,' + i).disabled = (i!=todaysI);
    }
  } catch (e) {}
}
function fillInContactInfo() {
  const person = idToReferral(getCookie('linkPages'));
  _('contactname').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
  //_('telnumber').href = 'tel:+' + person[ TableColumns['phone'] ];
  //_('smsnumber').href = 'sms:+' + person[ TableColumns['phone'] ];
  //_('emailcontact').href = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[9] + '&entry.873933093=';
  const numb = person[TableColumns['phone']].trim();

  _('referraltype').innerHTML = person[TableColumns['type']].replaceAll('_', ' ');
  _('referralorigin').innerHTML = prettyPrintRefOrigin(person[TableColumns['referral origin']]);
  if (numb == '') {
    // no number
    _('phonenumber').innerHTML = 'Undefined';
    _('telnumber').classList.add('disabled');
    _('smsnumber').classList.add('disabled');
  // } else if (getCookieJSON('prankNumberList').hasOwnProperty(numb)) {
  //   let onclickFunc = "showPrankedNumberInfoBox('" + getCookieJSON('prankNumberList')[numb] + "')";
  //   _('phonenumber').innerHTML = '<span class="prankedNumberWarning">' + numb + '</span> <i class="fa-solid fa-circle-question" onclick="'+onclickFunc+'"></i>';
  } else {
    _('phonenumber').innerHTML = numb;
  }
  _('email').innerHTML = person[TableColumns['email']];
  let addStr = person[TableColumns['street address']] + ' ' + person[TableColumns['city']] + ' ' + person[TableColumns['zip']];
  _('address').innerHTML = addStr;
  _('googlemaps').href = 'http://maps.google.com/?q=' + encodeURI(addStr);
  _('adName').innerHTML = person[TableColumns['ad name']];
  _('adDeck').href = CONFIG['home page links']['ad deck'];
  _('prefSprak').innerHTML = (person[TableColumns['lang']] == "") ? "Undeclared" : person[TableColumns['lang']];
  if (person[TableColumns['type']].toLowerCase().includes('family history')) {
    _('sendReferralBtn').setAttribute('onclick', "safeRedirect('fh_referral_info.html')");
  }
  fillInAttemptLog();
}
function prettyPrintRefOrigin(x) {
    switch (x.toLowerCase()) {
        case 'fb':
            return 'Facebook';
        case 'web':
            return 'Mission Website';
        case 'wix':
            return 'Mission Website';
        case 'ig':
            return 'Instagram';
        default:
            return x;
    }
}

fillInContactInfo();
  </script>

  </body>
</html>
