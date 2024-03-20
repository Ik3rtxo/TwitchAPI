<?php

$client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';
$client_secret = 've1fjp9t5n3eygo0vu6igoxg6mj0i6';

$post_data = array(
'client_id' => $client_id,
'client_secret' => $client_secret,
'grant_type' => 'client_credentials'
);

$ch1 = curl_init();
curl_setopt($ch1, CURLOPT_URL, 'https://id.twitch.tv/oauth2/token');
curl_setopt($ch1, CURLOPT_POST, 1);
curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);

curl_setopt(
    $ch1,
    CURLOPT_HTTPHEADER,
    array(
    'Content-Type: application/x-www-form-urlencoded'
    )
);

$response1 = curl_exec($ch1);

if (curl_errno($ch1)) {
    echo 'Error: ' . curl_error($ch1);
}

curl_close($ch1);

$response_data1 = json_decode($response1, true);
$access_token = $response_data1['access_token'];

$ch2 = curl_init();

$url = "https://api.twitch.tv/helix/streams";

curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_HTTPGET, true);

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

$resultados = array();

foreach ($response2_decoded['data'] as $item) {
    $titulo = $item['title'];
    $nombreUsuario = $item['user_name'];
    $resultados[] = array(
        'user_name' => $nombreUsuario,
        'title' => $titulo
    );
}

$resultadoFinal = array(
    'data' => $resultados
);

header("Content-Type: application/json");
$response2_encoded = json_encode($resultadoFinal, JSON_PRETTY_PRINT);
echo $response2_encoded;
