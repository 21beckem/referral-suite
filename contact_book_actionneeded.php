<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Referrals</title>
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
      <div style="padding-bottom: 6px !important">
        <a>Your Referrals</a>
      </div>
      <tabsheader>
        <tab class="active">Action Needed</tab>
        <tab><button onclick="location.href='contact_book_all.php'">All</button></tab>
      </tabsheader>
    </div>
    <div style="height: 100px;"></div>
     
   <!-- List of items -->
  <div class="w3-container">
    <div id="yourreferrals"></div>
    <div id="yourfollowups"></div>
  </div>


   <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(3);
    ?>

   </div>
   <script>
makeListClaimedPeople(CLAIMED);
makeListFollowUpPeople(FOLLOW_UPS);

function makeListClaimedPeople(arr) {
  let output = '';
  for (let i = 0; i < arr.length; i++) {
    const per = arr[i];
    let notifPoint = '';
    if (!hasPersonBeenContactedToday(per)) {
      notifPoint += `<div class="w3-left-align w3-circle" style="position:relative; color:red; right:-18px; top:-30px; font-size:25px; font-weight:bold; height:0;">!</div>`;
    }
    const elapsedTime = timeSince_formatted(new Date(per[TableColumns['date']]));
    output += `<aa onclick="saveToLinkPagesThenRedirect(` + per[TableColumns['id']] + `, this)" href="contact_info.php" class="person-to-click">
      <div class="w3-bar" style="display: flex;">
        <div class="w3-bar-item w3-circle">
          <div class="w3-left-align follow_up_person" style="width:20px;height:20px; margin-top: 22px; font-size:22px">
            <i class="fa-solid fa-circle" style="color:#ffa514"></i>
          </div>
          ` + notifPoint + `
        </div>
        <div class="w3-bar-item">
          <span class="w3-large">` + per[TableColumns['first name']] + ' ' + per[TableColumns['last name']] + `</span><br>
          <span>` + elapsedTime + `</span><br>
          <span>` + per[TableColumns['type']].replaceAll('_', ' ') + `</span>
        </div>
      </div>
    </aa>`;
  }
  _('yourreferrals').innerHTML = output;
}
function makeListFollowUpPeople(arr) {
  let output = '';
  for (let i = 0; i < arr.length; i++) {
    const per = arr[i];
    const elapsedTime = formatDateRelativeToToday(new Date(per[TableColumns['next follow up']]));
    output += `<aa onclick="saveToLinkPagesThenRedirect(` + per[TableColumns['id']] + `, this)" href="follow_up_on.php" class="person-to-click">
      <div class="w3-bar" style="display: flex;">
        <div class="w3-bar-item w3-circle">
          <div class="w3-left-align follow_up_person" style="width:20px;height:20px; margin-top: 22px; font-size:22px">
            <i class="fa-solid fa-clock" style="color:#ffa514"></i>
          </div>
        </div>
        <div class="w3-bar-item">
          <span class="w3-large">` + per[TableColumns['first name']] + ' ' + per[TableColumns['last name']] + `</span><br>
          <span>` + elapsedTime + `</span><br>
          <span>` + per[TableColumns['teaching area']] + `</span>
        </div>
      </div>
    </aa>`;
  }
  _('yourfollowups').innerHTML = output;
}
function formatDateRelativeToToday(inputDate) {
  var currentDate = new Date();
  var tomorrowDate = new Date(currentDate);

  var differenceMs = inputDate.getTime() - currentDate.getTime();
  var differenceDays = Math.floor(differenceMs / (1000 * 60 * 60 * 24))+1;
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
    timeStr = Math.abs(differenceDays) + " days";
    color = 'var(--warning-orange)';
  } else {
    timeStr = "invalid date";
  }
  if (differenceDays < -4) {
    color = 'var(--warning-red)';
  }
  return '<a style="color:' + color + '"><i class="fa fa-info-circle"></i> ' + timeStr + '</a>';
}
function timeSince_formatted(date) {
  var seconds = Math.floor((new Date() - date) / 1000);
  var interval = seconds / 31536000;
  let color = 'var(--all-good-green)';
  let timeStr = Math.floor(seconds) + " seconds";
  let found = false;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " years";
    color = 'var(--warning-red)';
  }
  interval = seconds / 2592000;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " months";
    color = 'var(--warning-red)';
  }
  interval = seconds / 86400;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " days";
    if (interval > 10.0) {
      color = 'var(--warning-red)';
    } else if (interval < 4.0) {
      color = 'var(--all-good-green)';
    } else {
      color = 'var(--warning-orange)';
    }
  }
  interval = seconds / 3600;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " hours";
    color = 'var(--all-good-green)';
  }
  interval = seconds / 60;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " minutes";
    color = 'var(--all-good-green)';
  }
  return '<a style="color:' + color + '"><i class="fa fa-info-circle"></i> ' + timeStr + '</a>';
}
    </script>
</body>
</html>
