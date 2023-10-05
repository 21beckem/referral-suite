<?php
require_once('require_area.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Complete SMS</title>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/0bddc0a0f7.js" crossorigin="anonymous"></script>
        <link href='https://fonts.googleapis.com/css?family=Advent Pro' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="https://21beckem.github.io/WebPal/WebPal.css">
    <script src="https://21beckem.github.io/WebPal/WebPal.js"></script>
    <script src="jsalert.js"></script>
    <script src="everyPageFunctions.php"></script>
    <script src="fox.js"></script>
        <script src="https://21beckem.github.io/SheetMap/sheetmap.js"></script>
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="manifest.webmanifest">
    <meta name="theme-color" content="#462c6a">
        <style>
#selectRefType {
    width: 100%;
    padding: 5px;
    border-radius: 3px;
}
#messageExamples {
    width: 100%;
    padding: 0px 10px;
}
.googleMessage {
    width: 100%;
    background-color: rgb(111, 221, 255);
    padding: 10px 15px;
    border-radius: 20px;
	  white-space: pre-line;
    color: var(--black);
}
@media (prefers-color-scheme: dark) {
  .googleMessage {
    background-color: rgb(23 158 199);
  }
}
.useThisTemplateBtn {
    padding: 5px 20px;
    border-radius: 3px;
    margin-top: 10px;
    border: 0;
    background-color: var(--sms-color);
    color: white;
    width: max-content;
}
input[type="text"] {
  width: 100%;
}
.ljus {
	background-color: #c5f2ff71;
}
.ljus.selected {
	background-color: #cfaeff;
}
    </style>
  </head>
  <body>
    <!-- Top Bar -->
    <div id="topHeaderBar" class="w3-top">
        <div class="w3-cell-row w3-area-blue">
            <div style="padding: 0px!important;">
                <a>Complete SMS</a>
            </div>
        </div>
    </div>
	<div style="height: 70px;"></div>
	<!-- Search bar -->
	<div id="completerItemsParent" class="w3-container w3-white w3-padding w3-card-subtle">
	</div>
    <div class="w3-panel w3-card-subtle w3-light-gray w3-padding-16">
		<div id="MessageOutput" class="googleMessage"></div>
		<button onclick="sendTheMessage()" class="useThisTemplateBtn">Send</button>
    </div>
	<a id="fakeLinkToClickToSend" href="" style="display: none;" target="_parent"></a>

    <script src="message_completer.js" current-page="sms"></script>

    <!-- Bottom Nav Bar -->
  <?php
  require_once('make_bottom_nav.php');
  make_bottom_nav(3);
  ?>

  </body>
</html>