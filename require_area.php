<?php

    session_start();
    if (!isset($_COOKIE['teamId'])) {
        header('location: login.php');
    }
    require_once('sql_tools.php');

    $__MISSIONINFO = json_decode($_COOKIE['missionInfo']);
    $__TEAM = getTeam();
    
    function getTeam() {
        global $__MISSIONINFO;
        $tmArr = readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `teams` WHERE `id`='.$_COOKIE['teamId'])[0];
        $tmCol = readTableColumns($__MISSIONINFO->mykey, 'teams');
        $team = array();
        for ($i=0; $i < count($tmCol); $i++) { 
            $team[ $tmCol[$i] ] = $tmArr[$i];
        }
        return (object) $team;
    }

    // usefull functions
    function getUnclaimed() {
        global $__MISSIONINFO, $__TEAM;
        return readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE `Referral Sent`="Not sent" AND `Claimed`="Unclaimed"');
    }
    function getClaimed() {
        global $__MISSIONINFO, $__TEAM;
        return readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE `Referral Sent`="Not sent" AND `Claimed`="'.$__TEAM->id.'"');
    }
    function getFollowUps() {

    }

?>