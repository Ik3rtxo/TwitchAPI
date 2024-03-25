<?php

require '../topsofthetops/token.php';

$client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';

$access_token = token();

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
