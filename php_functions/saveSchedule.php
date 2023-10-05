<?php

require_once('../require_area.php');

if (!isset($_POST['x']) || !isset($_POST['y']) || !isset($_POST['val'])) {
    die('Oj! A strange error occurred... Talk to your team leader');
}

//die($_POST['x'].', '.$_POST['y'].', '.$_POST['val']);

$data = json_decode(readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `schedule` WHERE 1')[0][0]);

$data[intval($_POST['y'])][intval($_POST['x'])] = $_POST['val'];

if (writeSQL($__MISSIONINFO->mykey, 'UPDATE `schedule` SET `json`="'.addslashes(json_encode( $data )).'" WHERE 1')) {
    die('success');
} else {
    die('Oj då! Something went wrong when saving that change. Try again!'); 
}

?>