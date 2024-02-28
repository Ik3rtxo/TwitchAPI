<?php

function top3($access_token){

    // Inicializar cURL para la segunda consulta
    $ch2 = curl_init();

    $url = "https://api.twitch.tv/helix/games/top";

    // Configurar la URL de la segunda solicitud
    curl_setopt($ch2, CURLOPT_URL, $url);

    // Especificar que queremos realizar una solicitud GET
    curl_setopt($ch2, CURLOPT_HTTPGET, true);

    $client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';

    // Establecer encabezados para la segunda solicitud
    curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
    'Authorization: ' . 'Bearer ' . $access_token,
    'Client-Id: ' . $client_id
    ));

    // Indicar que queremos recibir una respuesta
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar la segunda solicitud y obtener la respuesta
    $response2 = curl_exec($ch2);

    // Verificar si hay errores en la segunda solicitud
    if(curl_errno($ch2)){
        echo 'Error: ' . curl_error($ch2);
    }

    // Cerrar la conexión cURL de la segunda solicitud
    curl_close($ch2);

    // Decodificar el JSON a un array asociativo de PHP
    $response2_decoded = json_decode($response2, true);

    //Eliminamos lo de pagination
    unset($response2_decoded['pagination']);

    //Obtenemos solo los 3 primeros juegos
    $response2_decoded['data'] = array_slice($response2_decoded['data'], 0, 3);

    //$final_result_encoded = json_encode($final_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    return $response2_decoded;

}