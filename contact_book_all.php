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
  <style>
#tabsTable {
  width: 100%;
  margin: 0;
  padding: 0;
  border: none;
}
#tabsTable tr td button {
  width: 100%;
  text-align: center;
  background-color: inherit;
  color: inherit;
  border: none;
  margin: 0;
  padding: 0;
}
#tabsTable tr td {
  width: 50%;
  text-align: center;
  color: white;
  font-size: 20px;
}
#tabsTable tr td.active {
  background-color: #462c6a;
  border-bottom: 5px solid white;
}
  </style>
</head>
<body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div style="padding-bottom: 2px !important">
        <a>Your Referrals</a>
      </div>
      <table id="tabsTable" cellspacing=0>
        <tr>
          <td><button onclick="location.href='contact_book_actionneeded.php'">Action Needed</button></td>
          <td class="active">All</td>
        </tr>
      </table>
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
    let dotStyle = `<div class="w3-bar-item w3-circle">
        <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>`;
    let nextPage = 'contact_info.php';
    if (!hasPersonBeenContactedToday(per)) {
        dotStyle += `<div class="w3-left-align w3-circle" style="position:relative; color:red; right:-18px; top:-36px; font-size:25px; font-weight:bold; height:0;">!</div>`;
    }
    dotStyle += `</div>`;
    const elapsedTime = timeSince_formatted(new Date(per[TableColumns['date']]));
    output += `<aa onclick="saveToLinkPagesThenRedirect(` + per[TableColumns['id']] + `, this)" href="` + nextPage + `" class="person-to-click">
      <div class="w3-bar" style="display: flex;">` + dotStyle + `
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
    const elapsedTime = timeSince_formatted(new Date(per[TableColumns['next follow up']]));
    output += `<aa onclick="saveToLinkPagesThenRedirect(` + per[TableColumns['id']] + `, this)" href="follow_up_on.php" class="person-to-click">
      <div class="w3-bar" style="display: flex;">
        <div class="w3-bar-item w3-circle">
          <div class="w3-left-align follow_up_person" style="width:20px;height:20px; margin-top: 27px;">
            <i class="fa fa-calendar-check-o" style="color:#1d53b7; font-size:22px"></i>
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
