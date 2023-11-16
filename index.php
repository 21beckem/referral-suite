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
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="manifest.webmanifest">
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
#paintingWindow {
  position: fixed;
  width: 100%;
  height: 170px;
  /* background-image: url('img/jesusAndLamb1.jpg'); */
  background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(228,227,227,1) 100%);
  background-size: cover;
  background-position-y: center;
}
#paintingWindow img {
  position: relative;
  width: 140px;
  bottom: -12px;
  margin-left: 50%;
  transform: translateX(-50%);
  filter: drop-shadow(1px 4px 4px rgba(0,0,0,0.5));
}
#AreaName img {
  margin: 5px;
  display: inline-block;
  height: 1.5em;
  width: auto;
  transform: translate(0, -0.15em);
  border-radius: 50%;
}
#wholePageCard {
  background-color: var(--white);
  width: calc(100% + 2px);
  position: relative;
  margin-right: -1px;
  margin-left: -1px;
  margin-top: 170px;
  box-shadow: 0px 11px 20px 3px black;
  clip-path: inset(-15px 0px 0px 0px);
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Referral Suite<?php if(strpos($_SERVER['REQUEST_URI'],'beta') !== false) { echo(' Beta'); } ?></a>
      </div>
    </div>
    <div style="height: 60px;"></div>

    <div id="paintingWindow">
      <img src="img/templeClipart.png" alt="">
    </div>

    <div id="wholePageCard">
      <h3 id="AreaName" style="margin: 20px 0px -5px 10px"><img src="img/fox_profile_pics/<?php echo($__TEAM->color); ?>.svg" alt=""> <?php echo($__TEAM->name); ?></h3>

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
        <a href="tutorial_links.php">
          <div class="bigTipBtn" style="background-image: url('img/TutorialsBtnInvert.png'); border: 1px solid #f2bfff;"></div>
        </a>
      </div>
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
      <!-- <div class="full_width_home_btn">
        <a href="referral_archive.php">
          <div class="bigTipBtn" style="background-image: url('img/ArchiveInvert.jpg'); border: 1px solid #f2bfff;"></div>
        </a>
      </div> -->

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
