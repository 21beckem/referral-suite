<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unclaimed Referrals</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
  <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
  <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
  <script src="jsalert.js"></script>
  <script src="everyPageFunctions.php"></script>
  <script src="fox.js"></script>
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="manifest" href="manifest.webmanifest">
  <meta name="theme-color" content="#462c6a">
</head>

<body>
  <!-- Top Bar -->
  <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
    <div>
      <a>Unclaimed</a>
    </div>
  </div>
  <div style="height: 80px;"></div>

  <!-- List of items -->
  <div class="w3-container">
    <div id="unclaimedlist">
      <!--<a onclick="redirectAfterFunction(this)" href="claim_the_referral.html">
            <div class="w3-bar" style="display: flex;">
              <div class="w3-bar-item w3-circle">
                <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
              </div>
              <div class="w3-bar-item">
                <span class="w3-large"></span><br>
                <span></span><br>
                <span></span>
              </div>
            </div>
          </a>-->
    </div>
  </div>

  <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(4);
    ?>
  <script>
let output = '';
for (let i = 0; i < UNCLAIMED.length; i++) {
  const per = UNCLAIMED[i];
  let dotStyle = `<div class="w3-bar-item w3-circle">
      <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
  </div>`;
  const elapsedTime = timeSince_formatted(new Date(per[TableColumns['date']]));
  output += `<aa onclick="claimThisReferral(` + per[TableColumns['id']] + `, '`+per[TableColumns['first name']]+`', '`+per[TableColumns['last name']]+`')" class="person-to-click">
    <div class="w3-bar" style="display: flex;">` + dotStyle + `
      <div class="w3-bar-item">
        <span class="w3-large">` + per[TableColumns['first name']] + ' ' + per[TableColumns['last name']] + `</span><br>
        <span>` + elapsedTime + `</span><br>
        <span>` + per[TableColumns['type']].replaceAll('_', ' ') + `</span>
      </div>
    </div>
  </aa>`;
}
_('unclaimedlist').innerHTML = output;

function claimThisReferral(perId, fname, lname) {
  JSAlert.confirm('Are you sure you want to claim <br>'+fname+' '+lname+'<br> as <?php echo($__TEAM->name) ?>?', '', JSAlert.Icons.Info).then(res => {
    if (res) {
      safeRedirect('php_functions/claimReferral.php?perId=' + perId);
    }
  });
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