<?php

require_once('vendor/autoload.php');

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;


$auth = [
    'VAPID' => [
        'subject' => 'mailto:m1.g2.becker3@gmail.com',
        'publicKey' => 'BN2yWUCkOtoNVl5V9dwHj6rLYQn7-1YsnZw1Fpmc84hUtxKxd40JDC3XKw-uiByi95XXnZHhvTLAwd-lbtcpYvQ',
        'privateKey' => 'QLW1rdrBEKafhH7wfUznv_eh7j4JCIs2E4gh3uGrOvA',
    ],
];

$webPush = new WebPush($auth);

$subStr = '{"endpoint":"https://fcm.googleapis.com/fcm/send/cPPgJbV3zeE:APA91bGfMPj2uWd-lz6qI_OJGnDzzd2nV-MZjbtPncGpv_zgtZjis16a0I5zYoJzyhcAb4fwhxq1VHiN3_whav0645j_ExfyCUG9kAi311j7m9d-o7loH_TpXHHbZAzBh8K0gFSKSILc","expirationTime":null,"keys":{"p256dh":"BCYS2GZ_IH9mqsk2hz7ZyFuKDx5IjtkNWWIkJbwix5cXxm81xEwz-dLuZBujMIq9pP-XnxGiaM-DLLJ6AgLPNpc","auth":"ebnvEzEpLjl0VLxOJqaPFA"}}';

$report = $webPush->sendOneNotification(
    Subscription::create(json_decode($subStr,true))
    , '{"title":"Hi from php" , "body":"php is amazing!" , "url":"./?message=123"}', ['TTL' => 5000]);

print_r($report);



?>