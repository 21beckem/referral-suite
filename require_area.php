<?php

    session_start();
    if (!isset($_COOKIE['teamId'])) {
        header('location: login.php');
    }
    require_once('sql_tools.php');
    require_once('overall_vars.php');

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
    function makeConfigFromRows($rows) {
        $out = array();
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];
            if ($row[3] == 'json') {
                $out[ $row[5] ] = json_decode($row[6]);
            } else {
                $out[ $row[5] ] = $row[6];
            }
        }
        return $out;
    }
    function getConfig() {
        if (isset($_COOKIE['__CONFIG'])) {
            return json_decode($_COOKIE['__CONFIG']);
        }
        global $__MISSIONINFO;
        $rows = readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `settings` WHERE 1');
        $jsonStr = json_encode( makeConfigFromRows($rows) );
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