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

$deviceToken = 'fkKMYlClrS1ZXtrs5-BRoC:APA91bHQuM63iyRIMBb9eaAGt7h1erH48cNQzS0z2HU5W2Sdjkh84E-JZT2jmW6DtfQePfqF5HQdpQ5LNsz--CLTv8g9rqFy92piKb5tNY8t27jF8m-s-aoM9Ysawy-28yiL4Oa50-fb';
$result = sendFCMNotification($deviceToken);

var_dump($result);

?>