<?php

function sendFCMNotification($deviceToken) {
    $url = 'https://fcm.googleapis.com/fcm/send';
    $data = [
        'to' => $deviceToken,
        'notification' => [
            'body' => 'Yo! New Referral!',
            'title' => 'Referral Received',
            'image' => 'img/fox_profile_pics/red.svg'
        ]
    ];
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            "Authorization: key=AAAAjdtjTaQ:APA91bGYIyTdoqKCX0RwqwJBky84CsdJXf-pE_zUcoHHuR0Di6WAQs_1yoZqIHWJncYkyrsQh1tcd3SmXcdNtit9vHb4_40qX3p-PKQfSgaJMQ4k5hSYdtBb7c-xy-K5INsWE8tE9v0U",
            "Content-Type: application/json",
        ),
        CURLOPT_POSTFIELDS => json_encode($data),
    );
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}

$deviceToken = 'era-39GGI-qim6L9Todmdy:APA91bFUzh6IP0HZ3soGbxkCjDqujw3ZH5s91OQVz711xosL9b-JFuZtnJeOgLFhNHLWrLBgYU0_tzw0J8Y3zV_e4nWsW-jy6jDY44O0vcdBEKU-ewcb2kY3g56Vj9ZJCDkBzrHtD6wE';
$result = sendFCMNotification($deviceToken);

var_dump($result);

?>