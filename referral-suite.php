<?php

// if no area is defined, just cancel
if (!isset($_GET['area'])) {
    die('error. Missing info');
}
$areaName = $_GET['area'];
$raw_data = $_GET['data'];

// if area is SU, return just the SU stuff
if ($areaName == 'SU') {
    if ($raw_data != NULL) {
        SETSUSent(json_decode($raw_data));
    }
    die(json_encode( GETavailableSURefs() ));
}


// if peoples info have been changed, update it
if ($raw_data != NULL) {
    $received_data = json_decode($raw_data);
    if (property_exists($received_data, 'changed_people')) {
        if (count($received_data->changed_people) > 0) {
            SETPeopleData($received_data->changed_people);
        }
    }
    if (property_exists($received_data, 'claim_these')) {
        if (count($received_data->claim_these) > 0) {
            ClaimPeople($areaName, $received_data->claim_these);
        }
    }
}

// get current data for this area
$myObj = new stdClass();
$myObj->overall_data = new stdClass();
$myObj->overall_data->new_referrals = GETavailableRefs();
$myObj->area_specific_data = new stdClass();
$myObj->area_specific_data->my_referrals = GETPeopleData($areaName);
date_default_timezone_set("Sweden/Stockholm");
$myObj->area_specific_data->last_sync = date("D M d, Y G:i");

die( json_encode($myObj) );




// functions!! #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #

function readSQL($sqlStr, $convertRowsToArrays=TRUE) {
    // create SQL connection
    $conn = mysqli_connect("localhost", "reader", "reading_sql", "ssm_referrals");
    if ($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    // run sql
    $result = mysqli_query($conn, $sqlStr);
    // loop to store the data in an associative array.
    $out = array();
    $index = 0;
    while ($row = mysqli_fetch_assoc($result)) {
    	if ($convertRowsToArrays) {
        	$out[$index] = array_values($row);
        } else {
        	$out[$index] = $row;
        }
        $index++;
    }
    //close and return
    mysqli_close($conn);
    return $out;
}
function writeSQL($sqlStr) {
    // create SQL connection
    $conn = mysqli_connect("localhost", "SMOES", "Sannalarjungar9205", "ssm_referrals");
    if ($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    // send the query and return result
    $out = mysqli_query($conn, $sql);
    // close connection
    mysqli_close($conn);
}


function SETSUSent($people) {
    foreach ($people as $person) {
        writeSQL('UPDATE `SU_Referrals` SET `Send Status`="Sent" WHERE `id`='.$person[0]);
    }
}

function GETavailableSURefs() {
    return readSQL('SELECT * FROM `SU_Referrals` WHERE `Send Status`="Ready to send"');
}

function SETPeopleData($changed_people) {
    foreach ($changed_people as $person) {
        if ( !writeSQL('UPDATE `'.$person[0].'` SET `Referral Sent`="'.$person[3].'", `Teaching Area`="'.$person[5].'",  WHERE `id`='.$person[1]) ) {
            return FALSE;
        }
    }
}

function ClaimPeople($area, $claim_these) {
    foreach ($claim_these as $person) {
        writeSQL('UPDATE `'.$person[0].'` SET `Claimed`="'.$area.'" WHERE `Date and Time`='.$person[1]);
    }
}

function GETavailableRefs() {
    return GETPeopleData('Unclaimed', '`Date and Time`, `First Name`, `Last Name`');
}

function GETPeopleData($area, $columns='*') {
    $mbArr = readSQL('SELECT '.$columns.' FROM `Mormons_Bok_Request` WHERE `Claimed`="'.$area.'"');
    $vrArr = readSQL('SELECT '.$columns.' FROM `Missionary_Visit_Request` WHERE `Claimed`="'.$area.'"');
    $vagenArr = readSQL('SELECT '.$columns.' FROM `VTHOF_leads` WHERE `Claimed`="'.$area.'"');
    
    $out = array();
    for ($i = 0; $i < count($mbArr); $i++) {
        array_unshift($mbArr[$i], 'Mormons_Bok_Request');
        array_push($out, $mbArr[$i]);
    }
    for ($i = 0; $i < count($vrArr); $i++) {
        array_unshift($vrArr[$i], 'Missionary_Visit_Request');
        array_push($out, $vrArr[$i]);
    }
    for ($i = 0; $i < count($vagenArr); $i++) {
        array_unshift($vagenArr[$i], 'VTHOF_leads');
        array_push($out, $vagenArr[$i]);
    }
    return $out;
}









/*
{
    "overall_data": {
        "new_referrals": [
            ["Mormons Bok Request", "2023-04-24 12:30:08", "Monica Isaksson"]
        ]
    },
    "area_specific_data": {
        "my_referrals": [
            ["Mormons Bok Request", "2023-04-26T10:37:52.000Z", "SMOEs", "Not sent", "", "Ritva Jokinen", "Ritva", "Jokinen", 46708241216, "ritva8.jokinen@hotmail.com", "Syrenvägen 5A.lgh.1002", "Bua", "4326#", "ig"]
        ],
        "last_sync": "2023-04-26T11:17:11.707Z"
    }
}
*/

?>