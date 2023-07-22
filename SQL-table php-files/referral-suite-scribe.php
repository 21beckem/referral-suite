<?php

require __DIR__ . '/sql_tools.php';

$your_referral_table_name = 'All_Referrals';

// if no area is defined, just cancel
if (!isset($_GET['area'])) {
    die('error. Missing info');
}

if ($_GET['area'] == 'SuperCoolAndSecretQuery') {
	$q = addslashes($_GET['q']);
    // query function to find all persons that contain something related to the search the user gave. Had to write in every single column individually
	$out = readSQL('SELECT * FROM `All_Referrals` WHERE (`Referral Type` LIKE "%'.$q.'%") OR (`id` LIKE "%'.$q.'%") OR (`Date and Time` LIKE "%'.$q.'%") OR (`Referral Sent` LIKE "%'.$q.'%") OR (`Claimed` LIKE "%'.$q.'%") OR (`Teaching Area` LIKE "%'.$q.'%") OR (`AB Status` LIKE "%'.$q.'%") OR (`Full Name` LIKE "%'.$q.'%") OR (`Phonenumber` LIKE "%'.$q.'%") OR (`Email` LIKE "%'.$q.'%") OR (`Street` LIKE "%'.$q.'%") OR (`City` LIKE "%'.$q.'%") OR (`Zip` LIKE "%'.$q.'%") OR (`Språk` LIKE "%'.$q.'%") OR (`Platform` LIKE "%'.$q.'%") OR (`Ad Name` LIKE "%'.$q.'%") OR (`Next Follow Up` LIKE "%'.$q.'%") OR (`Follow Up Status` LIKE "%'.$q.'%") OR (`Follow Up Count` LIKE "%'.$q.'%") OR (`Sent Date` LIKE "%'.$q.'%") OR (`NI Reason` LIKE "%'.$q.'%") OR (`Attempt Log` LIKE "%'.$q.'%") OR (`Help Request` LIKE "%'.$q.'%") OR (`Level of Knowledge` LIKE "%'.$q.'%") LIMIT 50;');
	die( json_encode($out) );
}

$areaName = $_GET['area'];
$raw_data = $_GET['data'];
$searchCol = $_GET['searchCol'];


// if peoples info have been changed, update it
if ($raw_data != NULL) {
    $received_data = json_decode($raw_data);
    if (property_exists($received_data, 'changed_people')) {
        if (count($received_data->changed_people) > 0) {
            SETPeopleData($received_data->changed_people, $searchCol);
        }
    }
}

// get current data for this area
$myObj = new stdClass();
$myObj->overall_data = new stdClass();
$myObj->overall_data->new_referrals = GETPeopleData('Unclaimed');
$myObj->overall_data->follow_ups = GETFollowUpPeople();
$myObj->area_specific_data = new stdClass();
$myObj->area_specific_data->my_referrals = GETPeopleData($areaName);
$myObj->area_specific_data->last_sync = date("D M d, Y G:i");

if (isset($_GET['prettyprint'])) {
	echo('<pre>');
	var_dump($myObj);
	die('<pre>');
}
die( json_encode($myObj) );




// functions!! #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #

function SETPeopleData($changed_people, $sCol) {
    global $your_referral_table_name;
    foreach ($changed_people as $person) {
        if ( !updateTableRowFromArray($your_referral_table_name, '`id`='.$person[$sCol], $person) ) {
            return FALSE;
        }
    }
}
function str_includes($haystack, $needle) {
    return $needle !== '' && mb_strpos($haystack, $needle) !== false;
}
function GETFollowUpPeople() {
    global $your_referral_table_name;
    return readSQL('SELECT * FROM `'.$your_referral_table_name.'` WHERE `Next Follow Up` <= CURRENT_TIME'); // sql to get EVERY referral for any area that needs to be followed up on
}
function GETPeopleData($area) {
    global $your_referral_table_name;
    return readSQL('SELECT * FROM `'.$your_referral_table_name.'` WHERE `Claimed`="'.$area.'" AND `Referral Sent`="Not sent"'); // sql to get person that this inboxing area has claimed
}

?>