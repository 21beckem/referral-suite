<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Up</title>
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
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div style="padding-bottom: 6px!important;text-align:left!important">
        <a class="contact_info" id="contactname"></a>
      </div>
      <tabsheader>
        <tab><button onclick="location.href='contact_info.php'">Contact</button></tab>
        <tab class="active">Follow Up</tab>
        <?php if (getConfig()->{'General'}->{'show attempt log'}) { ?><tab><button onclick="location.href='person_timeline.php'">Timeline</button></tab><?php } ?>
      </tabsheader>
    </div>
    <div style="height: 100px;"></div>

    <!-- Contact Card -->
    <div class="w3-card">
        <div class="w3-cell-row w3-padding">
            <div class="w3-container w3-center w3-xlarge">Following Up</div>
        </div>
        <div class="w3-container w3-margin-top w3-margin-bottom">
            <div class="w3-left-align w3-small w3-opacity">Status</div>
            <div id="ABstatus" class="w3-left-align w3-large"></div>
        </div>
        <div id="showIfNotSent" style="display:none">
          <div class="w3-cell-row w3-padding">
              <div class="w3-container w3-center w3-large">Person not sent yet</div>
              <br>
          </div>
        </div>
        <div id="showIfSent" style="display:none">
          <div class="w3-container w3-margin-top w3-margin-bottom">
              <div class="w3-left-align w3-small w3-opacity">Sent on</div>
              <div id="lastAtt" class="w3-left-align w3-large"></div>
          </div>
          <div class="w3-container w3-margin-top w3-margin-bottom">
              <div class="w3-left-align w3-small w3-opacity">Next follow up</div>
              <div id="nextFollowUp" class="w3-left-align w3-large"></div>
          </div>
          <div class="w3-container w3-margin-top w3-margin-bottom">
            <div class="w3-left-align w3-small w3-opacity">Amount of times already followed up on</div>
            <div id="followUpCount" class="w3-left-align w3-large"></div>
          </div>
          <div class="w3-container w3-margin-top w3-margin-bottom">
            <div class="w3-left-align w3-small w3-opacity">Last follow up status</div>
            <div id="lastFollowUpStatus" class="w3-left-align w3-large"></div>
          </div>
          <div class="w3-container w3-margin-top w3-margin-bottom">
            <div class="w3-left-align w3-small w3-opacity">Area they are in</div>
            <div id="refLoc" class="w3-left-align w3-large"></div>
          </div>
          
          <div class="w3-container w3-margin-top w3-margin-bottom">
            <div class="w3-left-align w3-small w3-opacity">Team that sent the referral</div>
            <div id="refSender" class="w3-left-align w3-large w3-margin-bottom"></div>
          </div>
        </div>
    </div>
    <div style="height: 10px;"></div>

    <div class="w3-card" id="contactAreaCard" style="display: none;">
      <div class="w3-cell-row w3-padding">
          <div class="w3-container w3-center w3-xlarge">Contact the area</div>
      </div>

      <div class="w3-cell-row w3-padding-16">
          <div class="w3-container w3-cell w3-xxlarge w3-center">
              <a id="telnumber" href="" target="_parent">
                  <i class="fa fa-phone-square w3-text-call-color">
                    <div class="w3-tiny w3-opacity" style="height: 0;">Call</div>
                  </i>
              </a>
          </div>

          <div class="w3-container w3-cell w3-xxlarge w3-center">
              <a id="smsnumber" href="">
                  <i class="fa fa-comment w3-text-sms-color">
                    <div class="w3-tiny w3-opacity" style="height: 0;">SMS</div>
                  </i>
              </a>
          </div>
      </div>
  </div>

    <!-- Options to follow up -->
    <div id="iveContactedThemBox" class="w3-container w3-cell-row" style="margin-top: 40px; display:none">
      <div class="w3-container w3-cell w3-center">
        <a class="w3-button w3-xlarge w3-round-large w3-blue" href="follow_up_form.php">I've Sucessfully <br> Contacted <span id="refLoc2"></span></a>
      </div>
    </div>

   <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(3, '100px');
    ?>
   <script>
const teamInfos = <?php echo(json_encode( readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `teams` WHERE 1') )); ?>;
const areas = <?php echo(json_encode( readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `mission_areas` WHERE 1') )) ?>;
const dotStyle = {
  'green' : `<i class="fa-solid fa-circle" style="color:green"></i>`,
  'yellow_ring' : `<i class="fa-regular fa-circle" style="color:#ffa514"></i>`,
  'grey' : `<i class="fa-solid fa-circle" style="color:grey"></i>`,
  'gray' : `<i class="fa-solid fa-circle" style="color:grey"></i>`,
  'FU_ready' : `<i class="fa-solid fa-clock" style="color:#ffa514"></i>`,
  'yellow' : `<i class="fa-solid fa-circle" style="color:#ffa514"></i>`,
}
async function fillInFollowUpInfo() {
  const person = await idToReferral(getCookie('linkPages'));
  _('contactname').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
  _('lastAtt').innerHTML = new Date(person[TableColumns['sent date']]).toLocaleDateString("en-US", { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
  _('nextFollowUp').innerHTML = formatDateRelativeToToday( person[TableColumns['next follow up']] );
  _('ABstatus').innerHTML = dotStyle[ person[TableColumns['AB status']].toLowerCase() ] + ' ' + person[TableColumns['AB status']];
  let fuTimes = person[TableColumns['amount of times followed up']];
  _('lastFollowUpStatus').innerHTML = Object.keys(CONFIG['Follow Ups']['status delays'])[person[TableColumns['follow up status']]] || 'This is the first follow up';
  _('followUpCount').innerHTML = fuTimes + ((parseInt(fuTimes) == 1) ? ' time' : ' times');
  _('refLoc').innerHTML = person[TableColumns['teaching area']];
  _('refLoc2').innerHTML = person[TableColumns['teaching area']];
  _('refSender').innerHTML = teamInfos.filter(x=> parseInt(x[0])==parseInt(person[TableColumns['claimed area']]))[0][1];

  // find area number
  for (let i = 0; i < areas.length; i++) {
    if (areas[i][1] == person[TableColumns['teaching area']] && areas[i][2] != '') {
      _('contactAreaCard').style.display = '';
      _('telnumber').href = 'tel:' + areas[i][2];
      _('smsnumber').href = 'sms:' + areas[i][2];
      break;
    }
  }

  if (person[TableColumns['sent status']].toLowerCase() == 'sent') {
    _('iveContactedThemBox').style.display = '';
    _('showIfSent').style.display = '';
  } else {
    _('showIfNotSent').style.display = '';
    _('ABstatus').innerHTML = dotStyle['yellow_ring'] + ' Yellow';
  }
}
function formatDateRelativeToToday(inputDate) {
  if (inputDate == null) {
    return '<a style="color:grey">No follow up scheduled</a>';
  }
  inputDate = new Date(inputDate);
  let currentDate = new Date();
  let tomorrowDate = new Date(currentDate);

  let differenceMs = inputDate.getTime() - currentDate.getTime();
  let differenceDays = Math.floor(differenceMs / (1000 * 60 * 60 * 24))+1;
  let timeStr = '';
  let color = 'grey';
  if (differenceDays === 0) {
    timeStr = "today";
    color = 'var(--all-good-green)';
  } else if (differenceDays == 1) {
    timeStr = "tomorrow";
  } else if (differenceDays == -1) {
    timeStr = "yesterday";
    color = 'var(--warning-orange)';
  } else if (differenceDays > 1) {
    timeStr = "in " + differenceDays + " days";
  } else if (differenceDays < -1) {
    timeStr = Math.abs(differenceDays) + " days ago";
    color = 'var(--warning-orange)';
  } else {
    timeStr = "invalid date";
  }
  if (differenceDays < -4) {
    color = 'var(--warning-red)';
  }
  return '<a style="color:' + color + '">' + timeStr + '</a>';
}

fillInFollowUpInfo();

</script>

  </body>
</html>
