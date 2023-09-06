<?php

    session_start();
    if (!isset($_COOKIE['teamId'])) {
        header('location: login.php');
    }
    require_once('sql_tools.php');

    $__MISSIONINFO = json_decode($_COOKIE['missionInfo']);
    $__TEAM = getTeam();
    $__CONFIG = getConfig();
    
    function getTeam() {
        if (isset($_COOKIE['__TEAM'])) {
            return json_decode($_COOKIE['__TEAM']);
        }
        global $__MISSIONINFO;
        $tmArr = readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `teams` WHERE `id`='.$_COOKIE['teamId'])[0];
        $tmCol = readTableColumns($__MISSIONINFO->mykey, 'teams');
        $team = array();
        for ($i=0; $i < count($tmCol); $i++) { 
            $team[ $tmCol[$i] ] = $tmArr[$i];
        }
        setcookie('__TEAM', json_encode((object) $team), time() + (86400 * 1), "/"); // 86400 = 1 day
        return (object) $team;
    }
    function getConfig() {
        if (isset($_COOKIE['__CONFIG'])) {
            return json_decode($_COOKIE['__CONFIG']);
        }
        global $__MISSIONINFO;
        $jsonStr = readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `config` WHERE 1')[0][0];
        setcookie('__CONFIG', $jsonStr, time() + (86400 * 1), "/"); // 86400 = 1 day
        return json_decode($jsonStr);
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
        global $__MISSIONINFO, $__TEAM;
        return readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE `Next Follow Up` <= CURRENT_TIME');
    }

?>