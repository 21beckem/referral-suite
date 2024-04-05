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
#paintingWindow {
  position: fixed;
  width: 100%;
  height: 170px;
  background-image: url('img/jesusAndLamb1.jpg');
  /* background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(228,227,227,1) 100%); */
  background-size: cover;
  background-position: center;
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
  padding-bottom: 5px;
  clip-path: inset(-15px 0px 2px 0px);
}
.splash-screen {
  position: fixed;
  z-index: 9999999999;
  top: -100px;
  left: -100px;
  width: calc(100% + 200px);
  height: calc(100% + 200px);
  background-color: #7347B2;
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 1;
  transform: scale(1);
  animation: splashAnimWhole 0.6s 1.6s ease forwards;
}
.splash-screen img {
  z-index: 2;
  width: 240px;
  height: auto;
  transform: translateY(0px);
  border-radius: 0px;
  box-shadow: 0 2px 5px 0 transparent;
  animation: splashAnimImg 0.4s 0.4s ease forwards;
}
.splash-screen credit {
  z-index: 1;
  color: white;
  position: absolute;
  width: 240px;
  font-size: 30px;
  text-align: center;
  transform: translateY(0px);
  animation: splashAnimDev 0.6s 0.2s ease forwards;
}
@keyframes splashAnimWhole {
  0% { opacity: 1; transform: scale(1); }
  99.999% { opacity: 0; transform: scale(1.5); pointer-events: auto; }
  100% { opacity: 0; transform: scale(1.5); pointer-events: none; }
}
@keyframes splashAnimImg {
  0% { transform: translateY(0px); }
  100% { transform: translateY(-20px); border-radius: 50px; box-shadow: 0 2px 20px -10px rgb(0 0 0 / 16%); }
}
@keyframes splashAnimDev {
  0% { opacity: 0; transform: translateY(0px); }
  100% { opacity: 0.7; transform: translateY(140px); }
}
#topHomeBtns homebtn {
  width: calc(100% - 10px);
  border-radius: 10px;
  text-align: center;
  padding: 30px 15px;
  cursor: default;
}
.bigNumBigIcon {
  font-size: 70px;
  position: absolute;
  right: -5px;
  bottom: -10px;
  color: var(--very-light-purple);
  z-index: 0;
  opacity: 0.3;
  filter: blur(0px);
}
#topHomeBtns {
  width: calc(100% - 10px);
  display: flex;
  margin: 5px;
  flex-wrap: wrap;
}
#homeBtns {
  font-family: 'Advent Pro';
  width: calc(100% - 10px);
  display: flex;
  margin: 5px;
  flex-wrap: wrap;
}
homebtn {
  flex: 1 0 34%;
  background-color: var(--white);
  box-shadow: 0 2px 10px -6px rgb(0 0 0 / 20%);
  padding: 10px;
  margin: 5px;
  position: relative;
  display: flex;
  align-items: center;
  overflow: hidden;
  border-radius: 3px;
  min-height: 65px;
  cursor: pointer;
}
homebtn span {
  font-weight: bold;
}
homebtn i {
  padding: 5px;
}
    </style>
  </head>
  <body>
    <script>
      let firstTime = sessionStorage.getItem("first_time");
      if (!firstTime) {
        sessionStorage.setItem("first_time","1");
        document.write(`
          <div class="splash-screen">
            <credit id="dev-er"><span style="font-size:20px">Developed By:</span><br>Michael Becker</credit>
            <img src="logo.png" alt="App Icon">
          </div>
        `);
      }
    </script>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Referral Suite<?php if(strpos($_SERVER['REQUEST_URI'],'beta') !== false) { echo(' Beta'); } ?></a>
      </div>
    </div>
    <div style="height: 60px;"></div>

    <div id="paintingWindow">
      <!-- <img src="img/templeClipart.png" alt=""> -->
    </div>

    <div id="wholePageCard">
      <h3 id="AreaName" style="margin: 20px 0px -5px 10px"><img src="img/fox_profile_pics/<?php echo($__TEAM->color); ?>.svg" alt=""> <?php echo($__TEAM->name); ?></h3>

      <div id="topHomeBtns">
        <homebtn>
          <i class="fa-regular fa-circle bigNumBigIcon" style="font-size:200px; left: -80px; top: -30px;"></i>
          <a style="font-size: 50px;"><?php echo(count(getClaimed_stillContacting())); ?></a>
          <span style="font-family: 'Advent Pro'; opacity:0.7">People still being contacted</span>
        </homebtn>
        <homebtn>
          <i class="fa-solid fa-clock bigNumBigIcon" style="font-size:200px; left: -80px; top: -30px;"></i>
          <a style="font-size: 50px;"><?php echo(count(getFollowUps())); ?></a>
          <span style="font-family: 'Advent Pro'; opacity:0.7">People to follow up with</span>
        </homebtn>
      </div>
      
      <hr style="margin:10px">
      

      <div id="homeBtns">
        <homebtn href="tutorial_links.php">
          <i class="fa-solid fa-graduation-cap bigNumBigIcon"></i>
          <span>Tutorials</span>
        </homebtn>
        <homebtn id="MB_deliverLink" target="_blank" href="">
          <i class="fa-solid fa-book bigNumBigIcon"></i>
          <span>Book of Mormon<br>Delivery Form</span>
        </homebtn>
        <homebtn id="adDeck" target="_blank" href="">
          <i class="fa-solid fa-bullhorn bigNumBigIcon"></i>
          <span>Current Ads</span>
        </homebtn>
        <homebtn id="gToBusSuite" target="_blank" href="">
          <i class="fa-brands fa-meta bigNumBigIcon"></i>
          <span>Guide to<br>Bisiness Suite</span>
        </homebtn>
        <homebtn href="referral_archive.php">
          <i class="fa-solid fa-box-open bigNumBigIcon"></i>
          <span>Referral<br>Archive</span>
        </homebtn>
        <homebtn href="about.php">
          <i class="fa-solid fa-circle-info bigNumBigIcon"></i>
          <span>About</span>
        </homebtn>
      </div>

      <!-- Logout Button -->
      <button onclick="doubleCheckLogout()" class="w3-button w3-blue w3-xlarge w3-round-large" style="margin-left:10px; margin-top: 50px;">Sign Out</button>


    </div>
      <!-- Bottom Nav Bar -->
      <?php
      require_once('make_bottom_nav.php');
      make_bottom_nav(1);
      ?>
    <script>

setBigToolButtonLink('MB_deliverLink', CONFIG['Home Page']['book of mormon delivery form link']);
setBigToolButtonLink('adDeck', CONFIG['Home Page']['ad deck link']);
setBigToolButtonLink('gToBusSuite', CONFIG['Home Page']['business suite guidance link']);

function setBigToolButtonLink(elId, link) {
  // if id doesnt exist or blank, hide button
  //console.log(link);
  if (link==undefined || link.trim()=="") {
    _(elId).style.display = 'none';
    return;
  }
  _(elId).setAttribute('href', link);
}
document.querySelectorAll('#homeBtns homebtn').forEach(x =>
  x.onclick = function() {
    window.open(x.getAttribute('href'), x.getAttribute('target') || '_self');
  }
);
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
