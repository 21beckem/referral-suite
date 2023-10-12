<?php

session_start();
if (!isset($_COOKIE['teamId'])) {
    http_response_code(500);
    die('YOU! Trying to hack the system, are we? You need to be logged in!');
}

require_once('../require_area.php');

if (!isset($_GET['teamId']) || !isset($_GET['token'])) {
    http_response_code(500);
    die('Uh... something went wrong. You didn\'t provide the data I need');
}

$_SESSION['currentNotificationToken'] = $_GET['token'];

// if token already exists
$existsAlready = count( readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `tokens` WHERE `token`="'.$_GET['token'].'"') )  > 0;
if ($existsAlready) {
    http_response_code(202);
    die('Token already exists');
}

if (writeSQL($__MISSIONINFO->mykey, 'INSERT INTO `tokens` (`id`, `teamId`, `token`) VALUES (NULL, "'.$_GET['teamId'].'", "'.$_GET['token'].'")')) {
    echo('success');
} else {
    http_response_code(500);
    die('Oj då! Something went wrong with updating saving that token. Please try again'); 
}

?>