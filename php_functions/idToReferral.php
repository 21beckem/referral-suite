<?php

require_once('../require_area.php');

if (isset($_GET['q'])) {
	$out = readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE `id`="'.$_GET['q'].'" AND `Claimed`="'.$__TEAM->id.'" LIMIT 1')[0];
	die( json_encode($out) );
} else {
    die('[]');
}

?>