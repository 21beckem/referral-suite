<?php

    session_start();
    if (!isset($_COOKIE['teamId'])) {
        if(isset($doNotRedirectForRequireArea_JustReturnBlank)) {
            die('');
        } else {
            header('location: login.php');
        }
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
                $val = json_decode($row[6]);
            } else {
                $val = $row[6];
            }

            $out[ $row[2] ][ $row[5] ] = $val;
        }
        return $out;
    }
    function getConfig() {
        if (isset($_COOKIE['__CONFIG'])) {
            return json_decode($_COOKIE['__CONFIG']);
        }
        global $__MISSIONINFO;
        $rows = readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `settings` ORDER BY `settings`.`sort_order` ASC');
        $jsonStr = json_encode( makeConfigFromRows($rows) );
        setcookie('__CONFIG', $jsonStr, time() + (86400 * 1), "/"); // 86400 = 1 day
        return json_decode($jsonStr);
    }

    // usefull functions
    function getUnclaimed() {
        global $__MISSIONINFO, $__TEAM;
        return readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE `Referral Sent`="Not sent" AND `Claimed`="Unclaimed"');
    }
    function getClaimed_all() {
        global $__MISSIONINFO, $__TEAM;
        return readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE `Claimed`="'.$__TEAM->id.'"');
    }
    function getClaimed_stillContacting() {
        global $__MISSIONINFO, $__TEAM;
        return readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE `Referral Sent`="Not sent" AND `Claimed`="'.$__TEAM->id.'"');
    }
    function getFollowUps() {
        global $__MISSIONINFO, $__TEAM;
        return readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE `Referral Sent`="Sent" AND `Next Follow Up` <= CURRENT_TIME AND `Claimed`="'.$__TEAM->id.'"');
    }
    function getThisMonthsStats() {
        global $__MISSIONINFO, $__TEAM;
        $sent = count(readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE MONTH(`Date and Time`) = MONTH(now()) AND YEAR(`Date and Time`) = YEAR(now()) AND `Referral Sent` = "Sent"'));
        $notSent = count(readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE MONTH(`Date and Time`) = MONTH(now()) AND YEAR(`Date and Time`) = YEAR(now()) AND `Referral Sent` = "Not sent"'));
        $dropped = count(readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `all_referrals` WHERE MONTH(`Date and Time`) = MONTH(now()) AND YEAR(`Date and Time`) = YEAR(now()) AND `Referral Sent` = "Not interested"'));
        return [ $sent, $notSent, $dropped ];
    }
    function getReferralTypes() {
        if (isset($_COOKIE['__REFERRALTYPES'])) {
            return json_decode($_COOKIE['__REFERRALTYPES']);
        }
        global $__MISSIONINFO;
        $list = readSQL($__MISSIONINFO->mykey, 'SELECT * FROM `referral_types` WHERE 1');
        $types = array();
        for ($i=0; $i < count($list); $i++) { 
            $types[ $list[$i][1] ] = $list[$i][2];
        }
        setcookie('__REFERRALTYPES', json_encode((object) $types), time() + (86400 * 1), "/"); // 86400 = 1 day
        return (object) $types;
    }

?>