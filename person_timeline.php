<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Up</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
    <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
    <style>
#timelineBox {
  width: 100%;
  padding: 10px;
}
event {
  width: 100%;
  display: flex;
  margin: 0;
  padding: 0;
  align-items: center;
}
event .date {
  opacity: 0.6;
  flex: 3;
  text-align: center;
}
event .arrowgap {
  flex: 0.5;
  max-width: 13px;
  height: 75px;
  border-left: 3px solid rgba(100,100,100,0.4);
}
event .arrowgap .arrow {
  height: 0px;
  width: 0px;
  transform: Translate(-10px, 20px);
  border: 15px solid white;
  border-color: transparent var(--light-grey) transparent transparent;
}
event .rightside {
  flex: 15;
  padding: 5px 10px;
  min-width: 0;
}
event .rightside .box {
  background-color: var(--light-grey);
  width: 100%;
  height: 65px;
  padding: 5px 10px;
  min-width: 0;
}
event .rightside .box .title {
  font-size: 20px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top w3-cell-row w3-area-blue">
      <div style="padding-bottom: 6px!important;text-align:left!important">
        <a class="contact_info" id="contactname"></a>
      </div>
      <tabsheader>
        <tab><button onclick="location.href='contact_info.php'">Contact</button></tab>
        <tab><button onclick="location.href='follow_up_on.php'">Follow Up</button></tab>
        <tab class="active">Timeline</tab>
      </tabsheader>
    </div>
    <div style="height: 100px;"></div>

    <div id="timelineBox">
      <!-- <event>
        <div class="date">Mar 23<br>2024</div>
        <div class="arrowgap">
          <div class="arrow"></div>
        </div>
        <div class="rightside">
          <div class="box">
            <div class="title">System Start Right Now Okay? Haha</div>
            <div class="author">Becker</div>
          </div>
        </div>
      </event> -->
    </div>



   <!-- Bottom Nav Bar -->
    <?php
    require_once('make_bottom_nav.php');
    make_bottom_nav(3, '100px');
    ?>
   <script>
let tmln = [];
async function fillInTimeline() {
  const person = await idToReferral(getCookie('linkPages'));
  _('contactname').innerHTML = person[TableColumns['first name']] + ' ' + person[TableColumns['last name']];
  let rawTimeline = person[TableColumns['timeline']];
  try {
    tmln = JSON.parse(rawTimeline);
  } catch (e) {
    JSAlert.alert("Looks like there's a problem with this person's timeline. Contact the developer or your team leader.", 'Oh No!', JSAlert.Icons.Failed);
    return;
  }
  const Months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  for (let i = 0; i < tmln.length; i++) {
    const event = tmln[i];
    let d = new Date(event.date);
    let author = event.author || 'Referral Panel';
    _('timelineBox').innerHTML += `
      <event>
        <div class="date">` + Months[d.getMonth()] + ' ' + d.getDate() + `<br>` + d.getFullYear() + `</div>
        <div class="arrowgap">
          <div class="arrow"></div>
        </div>
        <div class="rightside">
          <div class="box" onclick="viewEvent(` + i + `)">
            <div class="title">` + event.title + `</div>
            <div class="author">` + author + `</div>
          </div>
        </div>
      </event>`;
  }
}
fillInTimeline();

function viewEvent(eventI) {
  let event = tmln[eventI];
  JSAlert.alert('<pre style="text-align:left; text-wrap: wrap; font-size: small">'+JSON.stringify(event, null, 2)+'</pre>', event.title, JSAlert.Icons.Information);
}

</script>

  </body>
</html>
