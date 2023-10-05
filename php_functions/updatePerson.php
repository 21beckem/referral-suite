<?php

session_start();
if (!isset($_COOKIE['teamId'])) {
    http_response_code(500);
    die('YOU! Trying to hack the system, are we? You need to be logged in!');
}

require_once('../require_area.php');

if (!isset($_GET['per'])) {
    http_response_code(500);
    die('Uh... something went wrong. You didn\'t provide the data I need');
}
$per = json_decode($_GET['per']);

if (updateTableRowFromArray($__MISSIONINFO->mykey, 'all_referrals', '`id`="'.$per[1].'"', $per)) {
    echo('success');
} else {
    http_response_code(500);
    die('Oj då! Something went wrong with updating this person. Please try again'); 
}

?>