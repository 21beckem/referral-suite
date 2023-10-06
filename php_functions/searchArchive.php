<?php

require_once('../require_area.php');

if (isset($_GET['q'])) {
	$q = addslashes($_GET['q']);
    // query function to find all persons that contain something related to the search the user gave. Had to write in every single column individually
	$out = readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE (`Referral Type` LIKE "%'.$q.'%") OR (`id` LIKE "%'.$q.'%") OR (`Date and Time` LIKE "%'.$q.'%") OR (`Referral Sent` LIKE "%'.$q.'%") OR (`Claimed` LIKE "%'.$q.'%") OR (`Teaching Area` LIKE "%'.$q.'%") OR (`AB Status` LIKE "%'.$q.'%") OR (`First Name` LIKE "%'.$q.'%") OR (`Last Name` LIKE "%'.$q.'%") OR (`Phonenumber` LIKE "%'.$q.'%") OR (`Email` LIKE "%'.$q.'%") OR (`Street` LIKE "%'.$q.'%") OR (`City` LIKE "%'.$q.'%") OR (`Zip` LIKE "%'.$q.'%") OR (`Lang` LIKE "%'.$q.'%") OR (`Platform` LIKE "%'.$q.'%") OR (`Ad Name` LIKE "%'.$q.'%") OR (`Next Follow Up` LIKE "%'.$q.'%") OR (`Follow Up Status` LIKE "%'.$q.'%") OR (`Follow Up Count` LIKE "%'.$q.'%") OR (`Sent Date` LIKE "%'.$q.'%") OR (`NI Reason` LIKE "%'.$q.'%") OR (`Attempt Log` LIKE "%'.$q.'%") OR (`Help Request` LIKE "%'.$q.'%") OR (`Level of Knowledge` LIKE "%'.$q.'%") ORDER BY `id` DESC LIMIT 50;');
	die( json_encode($out) );
} else {
    die('[]');
}

?>