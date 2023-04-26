<?php
error_reporting(1);

echo('<pre>');
$arr = readSQL('SELECT * FROM `Mormons_Bok_Request` WHERE `id`>20');
echo( json_encode($arr) );
echo('</pre>');
die();

// if no area is defined, just cancel
if (isset($_GET['area'])) {
    die('error. Missing info');
}
$area = $_GET['area'];
$raw_data = $_GET['data'];

// if area is SU, return just the SU stuff
if ($area == 'SU') {
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
            ClaimPeople($area, $received_data->claim_these);
        }
    }
}

// get current data for this area




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

/*
{
    "overall_data": {
        "referrals_available": "",
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