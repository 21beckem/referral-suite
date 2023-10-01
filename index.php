<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
    <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <link rel="icon" type="image/png" href="/referral-suite-manager/logo.png" />
    <script src="https://21beckem.github.io/SheetMap/sheetmap.js"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/referral-suite-manager/manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
    <style>
#totReferralsBar, #oldReferralBar {
  background: linear-gradient(90deg, rgb(94 215 74) 50%, rgba(158,0,0,1) 100%);
  background-size: 80vw;
  background-repeat: no-repeat;
  background-color: rgba(158,0,0,1);
}
#inbucksValue {
  color: #508d00;
}
#inbucksValue::before {
  font-size: 15px;
  content: "$";
  color: #508d009c;
}
#inbucksValue::after {
  font-size: 15px;
  content: "$";
  color: transparent;
}
#foxWindow {
  position: fixed;
  background-color: blue;
  width: 100%;
  height: 170px;
  background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(228,227,227,1) 100%);
}
#foxWindow img {
  position: relative;
  width: 170px;
  bottom: -20px;
}
#wholePageCard {
  background-color: var(--white);
  width: 100%;
  position: relative;
  margin-top: 170px;
  box-shadow: 0px 11px 20px 3px black;
}
@media (prefers-color-scheme: dark) {
  #foxWindow {
    color-scheme: dark;
    background: radial-gradient(circle, rgb(68 68 68) 0%, rgb(38 38 38) 100%);
  }
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Referral Suite</a>
      </div>
    </div>
    <div style="height: 60px;"></div>

    <div id="foxWindow">
      <center><img src="img/fox_profile_pics/<?php echo($__TEAM->color); ?>.svg" alt=""></center>
    </div>

    <div id="wholePageCard">
      <h3 style="margin: 20px 0px -5px 10px"><?php echo($__TEAM->name); ?></h3>

      <div class="w3-container" style="padding-bottom: 5px;">
        <table style="width: 100%;">
          <tr>
            <td style="width: 60%; position: relative;">
              <div id="streakBox">
                <img src="img/streak1.png" alt="streak_ico" style="width: 100%;">
                <a id="streakBoxNum" style="position:absolute; top: 0; left: 27px; font-size: 30px; color: #F203FF;">0</a>
              </div>
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 30%; font-size: 25px;">
              <img src="img/inbucks1.png" alt="inbucks_ico" style="width: 50%; margin-left: 50%; transform: translateX(-50%);">
              <a id="inbucksValue" style="display: block; width: 100%; text-align: center;">0</a>
            </td>
          </tr>
        </table>
        <center>
          <div id="leaderboardBtn" onclick="safeRedirect('fox_leaderboard.html')" class="w3-xlarge w3-round-large">Leaderboard</div>
        </center>
      </div>

      <!-- Progress bars -->
      <div class="w3-border-bottom">
        <table style="width: 100%;">
          <tr>
            <td style="width: 80%; padding-left: 10px;">
              <div class="w3-light-gray w3-round-medium">
                <div id="totReferralsBar" class="w3-round-medium" style="height: 24px; width: 0;"></div>
              </div>
            </td>
            <td id="totReferrals" class="w3-center w3-large" style="width: 20px; padding-left: 10px; padding-right: 10px;"></td>
          </tr>
        </table>
        <div class="w3-opacity" style="padding-left: 12px; padding-bottom: 5px">Claimed referrals</div>
      </div>

      <div class="w3-border-bottom" style="padding-top: 10px">
        <table style="width: 100%;">
          <tr>
            <td style="width: 80%; padding-left: 10px;">
              <div class="w3-light-gray w3-round-medium">
                <div id="oldReferralBar" class="w3-round-medium" style="height: 24px; width: 0;"></div>
              </div>
            </td>
            <td id="agebyday" class="w3-center w3-large" style="width: 20px; padding-left: 10px; padding-right: 10px;"></td>
          </tr>
        </table>
        <div class="w3-opacity" style="padding-left: 12px; padding-bottom: 5px">Oldest referral age (days)</div>
      </div>

      <div class="w3-xlarge" style="margin-top: 15px; margin-left: 20px;">Tools</div>
      <div class="full_width_home_btn">
        <a id="MB_deliverLink" target="_blank" href="">
          <div class="bigTipBtn" style="background-image: url('img/BookofMormonDeliverInvert.jpg'); border: 1px solid #f2bfff;"></div>
        </a>
      </div>
      <div class="full_width_home_btn">
        <a id="adDeck" target="_blank" href="">
          <div class="bigTipBtn" style="background-image: url('img/CurrentAdsInvert.jpg'); border: 1px solid #f2bfff;"></div>
        </a>
      </div>
      <div class="full_width_home_btn">
        <a id="gToBusSuite" target="_blank" href="">
          <div class="bigTipBtn" style="background-image: url('img/GuideToBusinessSuite.jpg'); border: 1px solid #f2bfff;"></div>
        </a>
      </div>
      <div class="full_width_home_btn">
        <a href="search_database.html">
          <div class="bigTipBtn" style="background-image: url('img/ArchiveInvert.jpg'); border: 1px solid #f2bfff;"></div>
        </a>
      </div>

      <!-- Logout Button -->
      <button onclick="doubleCheckLogout()" class="w3-button w3-blue w3-xlarge w3-round-large" style="margin-left:10px; margin-top: 50px;">Sign Out</button>


      <!-- Bottom Nav Bar -->
      <?php
      require_once('make_bottom_nav.php');
      make_bottom_nav(1);
      ?>
    </div>
    <script>
const my_referrals = <?php echo(json_encode( getClaimed() )); ?>;
let maxRefsAllowed = 15;
const currentRefCount = my_referrals.length;


let totReferralsBar = _("totReferralsBar");
let totReferrals = _("totReferrals");

totReferralsBar.style.width = Math.min(currentRefCount / maxRefsAllowed * 100, 100) + '%';

totReferrals.innerHTML = currentRefCount + '/' + maxRefsAllowed;

if (currentRefCount >= maxRefsAllowed) {
    totReferrals.classList.add('w3-text-red');
}
let maxRefAge = 7;
if (my_referrals.length > 0) {
    let oldReferralBar = _("oldReferralBar");
    let oldReferral = getOldestClaimedPerson()[2];
    let today = new Date();
    let oldDate = new Date(oldReferral);
    let dayDifference = Math.round((today.getTime() - oldDate.getTime()) / (1000 * 60 * 60 * 24));
    oldReferralBar.style.width = Math.min(dayDifference / maxRefAge * 100, 100) + '%';

    _("agebyday").innerHTML = dayDifference + '/' + maxRefAge;

    if (dayDifference >= maxRefAge) {
        _("agebyday").classList.add('w3-text-red');
    }
} else {
    _("agebyday").innerHTML = '0/' + maxRefAge;
}
setBigToolButtonLink('MB_deliverLink', CONFIG['Home Page']['book of mormon delivery form link']);
setBigToolButtonLink('adDeck', CONFIG['Home Page']['ad deck link']);
setBigToolButtonLink('gToBusSuite', CONFIG['Home Page']['business suite guidance link']);

/*let streakBoxFilter = '';
switch (foxStreakExtendingStatus()) {
    case 'done for today':
        streakBoxFilter = '';
        break;
    case 'can extend':
        streakBoxFilter = 'grayscale(0.8) opacity(0.8)';
        break;

    default:
        streakBoxFilter = 'brightness(0.5) grayscale(1) opacity(0.2)';
}
_('streakBox').style.filter = streakBoxFilter;*/
_('streakBoxNum').innerHTML = <?php echo(count(json_decode($__TEAM->fox_streak))); ?>;
_('inbucksValue').innerHTML = <?php echo($__TEAM->fox_inbucks); ?>;

function setBigToolButtonLink(elId, link) {
  // if id doesnt exist or blank, hide button
  //console.log(link);
  if (link==undefined || link.trim()=="") {
    _(elId).parentElement.style.display = 'none';
    return;
  }
  _(elId).href = link;
}
function getOldestClaimedPerson() {
    let currentOldest = my_referrals[0];
    for (let i = 0; i < my_referrals.length; i++) {
        const per = my_referrals[i];
        if (new Date(per[2]).getTime() < new Date(currentOldest[2]).getTime()) {
            currentOldest = per;
        }
    }
    return currentOldest;
}
function doubleCheckLogout() {
  JSAlert.confirm('Are you sure you want to sign out of <?php echo($__TEAM->name) ?>\'s Referral Suite?', '', JSAlert.Icons.Warning).then(res => {
    if (res) {
      safeRedirect('login.php');
    }
  });
}
    </script>
    
  </body>
</html>
