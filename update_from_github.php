<?php

//  DO NOT DELETE THIS FILE! This is what keeps Referral Suite up to date on the Mission's Web Server

shell_exec('find . -mindepth 1 -delete; git clone https://github.com/ssmission/referral-suite.git .');
die('gjort!');

function SETfollowUpStatus($changed_people) {
    foreach ($changed_people as $person) {
        $followUpDate = strtotime(date("Y-m-d").' + '.$person[3]);
        $dateTxt = date('D M d, Y G:i', $followUpDate);
        if ( !writeSQL('UPDATE `'.$person[0].'` SET `Next Follow Up`="'.$dateTxt.'", `Follow Up Status`="'.$person[2].'"  WHERE `id`='.$person[1]) ) {
            return FALSE;
        }
    }
}

?>
