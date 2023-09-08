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
    <link rel="icon" type="image/png" href="/referral-suite/logo.png" />
    <script src="https://21beckem.github.io/SheetMap/sheetmap.js"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/referral-suite/manifest.webmanifest">
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
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Referral Suite</a>
      </div>
    </div>
    <div style="height: 80px;"></div>

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
    <div class="w3-border-bottom w3-container" style="padding-bottom: 5px;">
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

    <div class="w3-center w3-xlarge" style="margin-top: 15px;">Welcome to Referral Suite!</div>
    <div class="" style="text-align: center;">Here is a list of things to check (in order) during your inbox shift!</div>
    <div class="w3-border-bottom" style="padding-bottom: 10px;">
      <table id="bigBtnsTable">
        <tr>
            <td>
              <a id="1_sync" href="#">
                <div class="bigTipBtn" style="background-image: url('img/1_home_btn.jpg');"></div>
              </a>
            </td>
            <td>
              <a id="2_contact" href="#">
                <div class="bigTipBtn" style="background-image: url('img/2_home_btn.jpg');"></div>
              </a>
            </td>
            <td>
              <a id="3_log" href="#">
                <div class="bigTipBtn" style="background-image: url('img/3_home_btn.jpg');"></div>
              </a>
            </td>
        </tr>
        <tr>
            <td>
              <a id="4_message" href="#">
                <div class="bigTipBtn" style="background-image: url('img/4_home_btn.jpg');"></div>
              </a>
            </td>
            <td>
              <a id="5_comments" href="#">
                <div class="bigTipBtn" style="background-image: url('img/5_home_btn.jpg');"></div>
              </a>
            </td>
            <td>
              <a id="6_report" onclick="sendToReportingForm()">
                <div class="bigTipBtn" style="background-image: url('img/6_home_btn.jpg');"></div>
              </a>
            </td>
        </tr>
      </table>
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
_('MB_deliverLink').href = CONFIG['home page links']['book of mormon delivery form'];
_('adDeck').href = CONFIG['home page links']['ad deck'];
_('gToBusSuite').href = CONFIG['home page links']['business suite help'];
setHomeBigBtnLink('1_sync');
setHomeBigBtnLink('2_contact');
setHomeBigBtnLink('3_log');
setHomeBigBtnLink('4_message');
setHomeBigBtnLink('5_comments');

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

function setHomeBigBtnLink(elId) {
    let link = CONFIG['home page links'][elId];
    const el = _(elId);
    if (link.includes('www.canva.com') || link.includes('docs.google.com/presentation')) {
        el.setAttribute('onclick', "openGoogleSlides('" + link + "')");
    } else if (!link.startsWith('http')) {
        el.href = link;
    } else {
        console.log('Unrecognized presentation link. Will open in new tab:' + link);
        el.href = link.replace("{Area}", area);
        el.setAttribute('target', '_blank');
    }
}
function openGoogleSlides(link) {
    setCookie('openThisSlides', link);
    safeRedirect('view_google_slides.html');
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
      location.href = 'login.php';
    }
  });
}
    </script>
    
  </body>
</html>