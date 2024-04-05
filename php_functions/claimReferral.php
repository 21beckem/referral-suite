<?php

require_once('../require_area.php');

if (!isset($_GET['perId'])) {
    die('YOU! You trying to break the system by not providing me with information!!');
}

if (writeSQL($__MISSIONINFO->mykey, 'UPDATE `all_referrals` SET `Claimed`="'.$__TEAM->id.'" WHERE `id`="'.$_GET['perId'].'"')) {
    addTimelineEvent($_GET['perId'], 'claimed', '', null, false);
    header('location: ../unclaimed_referrals.php');
} else {
    die('Oj då! Something went wrong when writing to the SQL table'); 
}

?>