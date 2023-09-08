<?php

require_once('../require_area.php');

if (!isset($_GET['type']) || !isset($_GET['refTyp'])) {
    http_response_code(500);
    die('YOU! You trying to break the system by not providing me with information!!');
}
$q = 'SELECT * FROM `templates` WHERE `Referral Type`="'.$_GET['refTyp'].'" AND `type`="'.$_GET['type'].'"';
//echo($q);
echo( json_encode(readSQL($__MISSIONINFO->mykey, $q)) );

?>