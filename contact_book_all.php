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
        <div class="contents">
          
          <aa onclick="saveToLinkPagesThenRedirect(` + per[TableColumns['id']] + `, this)" href="follow_up_on.php" class="person-to-click">
            <div class="w3-bar" style="display: flex;">
              <div class="w3-bar-item w3-circle">
                <div class="w3-left-align follow_up_person" style="width:20px;height:20px; margin-top: 27px;">
                  <i class="fa fa-calendar-check-o" style="color:#1d53b7; font-size:22px"></i>
                </div>
              </div>
              <div class="w3-bar-item">
                <span class="w3-large">Michael Becker</span><br>
                <span>5 Min</span><br>
                <span>I'm in nerd</span>
              </div>
            </div>
          </aa>

        </div>
      </div>
      <div class="header">
        <div class="bar"><div class="bar-line">Still Contacting</div></div>
        <div class="contents" id="stillContacting_box"></div>
      </div>

    </div>


   <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(3);
    ?>

   </div>
   <script>
const ALL_CLAIMED = <?php echo(json_encode( getClaimed_all() )) ?>;
sortAllPeopleToBoxes(ALL_CLAIMED);

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

  // sorted into AB colors. Now sort a little further and start pasting on the page

  let lst = peopleListToHTML(ALL_people);
  _('stillContacting_box').innerHTML = lst;
}

function ABstatus_toColor(col) {
  let tab = {
    'yellow' : '#ffa514',
    'green' : 'green',
    'gray' : 'gray',
    'grey' : 'gray'
  }
  if (!(col.toLowerCase() in tab)) {
    return col.toLowerCase();
  }
  return tab[col.toLowerCase()];
}
function peopleListToHTML(arr) {
  let output = '';
  for (let i = 0; i < arr.length; i++) {
    const per = arr[i];
    let dotStyle = `<div class="w3-bar-item w3-circle">
        <div class="w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px; background-color: ` + ABstatus_toColor(per[TableColumns['AB status']]) + `;"></div>`;
    dotStyle += `</div>`;
    const elapsedTime = timeSince_formatted(new Date(per[TableColumns['date']]));
    output += `<aa onclick="saveToLinkPagesThenRedirect(` + per[TableColumns['id']] + `, this)" href="contact_info.php" class="person-to-click">
      <div class="w3-bar" style="display: flex;">` + dotStyle + `
        <div class="w3-bar-item">
          <span class="w3-large">` + per[TableColumns['first name']] + ' ' + per[TableColumns['last name']] + `</span><br>
          <span>` + elapsedTime + `</span><br>
          <span>` + per[TableColumns['type']].replaceAll('_', ' ') + `</span>
        </div>
      </div>
    </aa>`;
  }
  return output;
}
function timeSince_formatted(date) {
  var seconds = Math.floor((new Date() - date) / 1000);
  var interval = seconds / 31536000;
  let timeStr = Math.floor(seconds) + " seconds";
  let found = false;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " years";
  }
  interval = seconds / 2592000;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " months";
  }
  interval = seconds / 86400;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " days";
  }
  interval = seconds / 3600;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " hours";
  }
  interval = seconds / 60;
  if (interval > 1 && !found) {
    found = true;
    timeStr = Math.round(interval) + " minutes";
  }
  return '<a style="color:gray"><i class="fa fa-info-circle"></i> ' + timeStr + '</a>';
}
    </script>
</body>
</html>
