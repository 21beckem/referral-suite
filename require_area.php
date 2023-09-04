<?php

    session_start();
    if (!isset($_COOKIE['teamId'])) {
        header('location: login.php');
    }
    $__TEAMID = $_COOKIE['teamId'];
    $__MISSIONINFO = json_decode($_COOKIE['missionInfo']);

?>