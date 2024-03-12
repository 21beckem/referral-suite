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

#yourreferrals div.header {
  width: 100%;
  margin-bottom: 15px;
}
#yourreferrals div.header div.bar {
  width: 100%;
  padding: 5px 20px;
  box-shadow: 0px 3px 10px -8px black;
  clip-path: inset(0px 0px -10px 0px);
  position: sticky;
  background-color: white;
  top: 95px;
}
#yourreferrals div.header div.contents {
  padding: 0px 10px;
}
#yourreferrals div.header div.bar div.bar-line {
  width: 100%;
  border-bottom: 2px solid #462c6a;
  font-size: 20px;
  color: #462c6a;
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
    <div style="height: 95px;"></div>
     
    <!-- List of items -->
    <div id="yourreferrals">
      <div class="header">
        <div class="bar"><div class="bar-line">Still Contacting</div></div>
        <div class="contents" id="stillContacting_box"></div>
      </div>
      <div class="header">
        <div class="bar"><div class="bar-line">Ready To Follow Up</div></div>
        <div class="contents" id="readyToFollowUp_box"></div>
      </div>
      <div class="header">
        <div class="bar"><div class="bar-line">Follow Up Waiting</div></div>
        <div class="contents" id="followUpWaiting_box"></div>
      </div>
      <div class="header">
        <div class="bar"><div class="bar-line">Being Taught</div></div>
        <div class="contents" id="beingTaught_box"></div>
      </div>
      <div class="header">
        <div class="bar"><div class="bar-line">Never Sent</div></div>
        <div class="contents" id="neverSent_box"></div>
      </div>
      <div class="header">
        <div class="bar"><div class="bar-line">Sent. Never Met</div></div>
        <div class="contents" id="neverMet_box"></div>
      </div>
      
    </div>


   <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(3);
    ?>

   </div>
   <script>
function sortAllPeopleToBoxes(ALL_people) {
  let srtd = {
    'yellow' : [],
    'green' : [],
    'gray' : [],
    'grey' : []
  }
  for (let i = 0; i < ALL_people.length; i++) {
    const per = ALL_people[i];
    srtd[per[TableColumns['AB status']].toLowerCase()].push(per);
  }
  srtd['grey'].push(...srtd['gray']);
  delete srtd['gray'];
  console.log(srtd);

  let lst = peopleListToHTML(srtd['yellow'].filter(x => x[TableColumns['sent status']]=='Not sent'), 'yellow');
  _('stillContacting_box').innerHTML = lst;

  lst = peopleListToHTML(srtd['yellow'].filter(x => x[TableColumns['sent status']]=='Sent' && dateInPast(x[TableColumns['next follow up']])), 'FU_ready');
  _('readyToFollowUp_box').innerHTML = lst;

  lst = peopleListToHTML(srtd['yellow'].filter(x => x[TableColumns['sent status']]=='Sent' && !dateInPast(x[TableColumns['next follow up']])), 'FU_waity');
  _('followUpWaiting_box').innerHTML = lst;

  lst = peopleListToHTML(srtd['green'], 'green');
  _('beingTaught_box').innerHTML = lst;

  lst = peopleListToHTML(ALL_people.filter(x => x[TableColumns['sent status']]=='Not interested'), 'grey');
  _('neverSent_box').innerHTML = lst;
  
  lst = peopleListToHTML(srtd['grey'].filter(x => x[TableColumns['sent status']]=='Sent'), 'grey');
  _('neverMet_box').innerHTML = lst;
}
const dotStyle = {
  'green' : `<i class="fa-solid fa-circle" style="color:green"></i>`,
  'yellow' : `<i class="fa-regular fa-circle" style="color:#ffa514"></i>`,
  'grey' : `<i class="fa-solid fa-circle" style="color:grey"></i>`,
  'FU_ready' : `<i class="fa-solid fa-clock" style="color:#ffa514"></i>`,
  'FU_waity' : `<i class="fa-solid fa-circle" style="color:#ffa514"></i>`,
}
function peopleListToHTML(arr, thisDotStyle="yellow") {
  let output = '';
  for (let i = 0; i < arr.length; i++) {
    const per = arr[i];
    let dotCode = dotStyle[thisDotStyle];
    output += `<aa onclick="saveToLinkPagesThenRedirect(` + per[TableColumns['id']] + `, this)" href="contact_info.php" class="person-to-click">
      <div class="w3-bar" style="display: flex;">
        <div class="w3-bar-item w3-circle">
          <div class="w3-left-align follow_up_person" style="width:20px;height:20px; margin-top: 10px; font-size:22px">
          ` + dotCode + `
          </div>
        </div>
        <div class="w3-bar-item">
          <span class="w3-large">` + per[TableColumns['first name']] + ' ' + per[TableColumns['last name']] + `</span><br>
          <span>` + per[TableColumns['type']].replaceAll('_', ' ') + `</span>
        </div>
      </div>
    </aa>`;
  }
  return output;
}
const ALL_CLAIMED = <?php echo(json_encode( getClaimed_all() )) ?>;
sortAllPeopleToBoxes(ALL_CLAIMED);
    </script>
</body>
</html>
