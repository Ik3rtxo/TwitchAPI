<?php

function top3($access_token)
{

    $ch2 = curl_init();

    $url = "https://api.twitch.tv/helix/games/top";

    curl_setopt($ch2, CURLOPT_URL, $url);
    curl_setopt($ch2, CURLOPT_HTTPGET, true);

    $client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';

    curl_setopt(
        $ch2,
        CURLOPT_HTTPHEADER,
        array(
        'Authorization: ' . 'Bearer ' . $access_token,
        'Client-Id: ' . $client_id
        )
    );

    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

    $response2 = curl_exec($ch2);

    if (curl_errno($ch2)) {
        echo 'Error: ' . curl_error($ch2);
    }

    curl_close($ch2);

    $response2_decoded = json_decode($response2, true);
    unset($response2_decoded['pagination']);
    $response2_decoded['data'] = array_slice($response2_decoded['data'], 0, 3);

    return $response2_decoded;
}
