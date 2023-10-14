<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Archive</title>
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
    <style>
#totReferralsBar, #oldReferralBar {
  background: linear-gradient(90deg, rgb(94 215 74) 50%, rgba(158,0,0,1) 100%);
  background-size: 80vw;
  background-repeat: no-repeat;
  background-color: rgba(158,0,0,1);
}
#referralSearchbar {
    padding: 3px;
    border: none;
    border-bottom: 2px solid gray;
}
.searchResult {
    border-bottom: 1px solid rgba(128, 128, 128, 0.5);
    /* border: 1px solid black; */
}
.searchResult .name {
    font-size: large;
}
.searchResult table td:first-child {
  width: 60%;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div>
        <a>Referral Archive</a>
      </div>
    </div>
    
    <!-- Progress bars -->
    <div class="w3-border-bottom" style="position: fixed; width: 100%; top: 69px; padding-top: 11px; background-color: var(--white); z-index: 10;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%; padding-left: 10px;">
                    <input id="referralSearchbar" type="text">
                </td>
                <td style="padding-left: 10px; padding-right: 10px;">
                    <button class="w3-button w3-blue w3-round-large w3-center w3-large" onclick="searchAndDisplayDatabaseReferrals()">Search</button>
                </td>
            </tr>
        </table>
        <div class="w3-opacity" style="padding-left: 12px; padding-bottom: 5px">Matched referrals:</div>
    </div>
    <div style="height: 160px;"></div>

    <div id="searchResultsBox" class="w3-container"></div>
    <div id="loadingAnim" style="display: none; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
      <div class="lds-dual-ring"></div>
    </div>

    <!-- Bottom Nav Bar -->
    <?php
      require_once('make_bottom_nav.php');
      make_bottom_nav(1);
    ?>
    <script>
async function searchAndDisplayDatabaseReferrals() {
  _('loadingAnim').style.display = '';
  const searchQ = _('referralSearchbar').value;

  let fetchURL = 'php_functions/searchArchive.php?q=' + encodeURIComponent(searchQ);
  const response = await safeFetch(fetchURL);
  const returnedRefs = await response.json();

  let output = '';
  for (let i = 0; i < returnedRefs.length; i++) {
    const per = returnedRefs[i];
    output += `<div class="searchResult" onclick="viewPersonInfo(` + JSON.stringify(per).replaceAll("'", "\\'").replaceAll('"', "'") + `)">
          <a class="name">` + per[TableColumns['first name']] + per[TableColumns['last name']] + `</a>
          <table style="width: 100%;">
            <tr>
              <td>
                <div class="w3-left-align w3-small w3-opacity"><i class="fa-solid fa-clock" style="color: var(--light-blue);"></i> ` + per[TableColumns['date']] + `</div>
              </td>
              <td>
                <div class="w3-left-align w3-small w3-opacity"><i class="fa-solid fa-signal" style="color: var(--call-color);"></i> ` + per[TableColumns['sent status']] + `</div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="w3-left-align w3-small w3-opacity"><i class="fa-solid fa-reply-all" style="color: var(--sms-color);"></i> ` + per[TableColumns['type']] + `</div>
              </td>
              <td>
                <div class="w3-left-align w3-small w3-opacity"><i class="fa-solid fa-chalkboard-user" style="color: var(--red);"></i> ` + per[TableColumns['teaching area']] + `</div>
              </td>
            </tr>
          </table>
      </div>`;
  }
  if (returnedRefs.length == 0) {
    output = 'No results';
  } else if (output == '') {
    output = "There's been an error...";
  }
  _('loadingAnim').style.display = 'none';
  _('searchResultsBox').innerHTML = output;
}
    </script>
    
  </body>
</html>
