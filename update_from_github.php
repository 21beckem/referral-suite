<?php

//  DO NOT DELETE THIS FILE! This is what keeps Referral Suite up to date on the Mission's Web Server

shell_exec('find . -mindepth 1 -delete; git clone https://github.com/ssmission/referral-suite.git .');
die('gjort!');

?>
