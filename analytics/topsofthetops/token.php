<?php

function token(){

    //Parametro que indica cada cuanto se actualiza la informacion
    //$time = $_GET['since'];

    // Datos de autenticación
    $client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';
    $client_secret = 've1fjp9t5n3eygo0vu6igoxg6mj0i6';

    // Datos para enviar en la solicitud POST
    $post_data = array(
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'client_credentials'
    );

    // Inicializar cURL para la primera consulta
    $ch1 = curl_init();

    // Configurar la URL de la primera solicitud
    curl_setopt($ch1, CURLOPT_URL, 'https://id.twitch.tv/oauth2/token');

    // Especificar que queremos realizar una solicitud POST
    curl_setopt($ch1, CURLOPT_POST, 1);

    // Pasar los datos a enviar en la solicitud POST
    curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query($post_data));

    // Indicar que queremos recibir una respuesta
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);

    // Establecer encabezados
    curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded'
    ));

    // Ejecutar la primera solicitud y obtener la respuesta
    $response1 = curl_exec($ch1);

    // Verificar si hay errores en la primera solicitud
    if(curl_errno($ch1)){
        echo 'Error: ' . curl_error($ch1);
    }

    // Cerrar la conexión cURL de la primera solicitud
    curl_close($ch1);

    // Decodificar la respuesta JSON de la primera solicitud
    $response_data1 = json_decode($response1, true);

    // Extraer el token de acceso de la primera respuesta
    $access_token = $response_data1['access_token'];
    //$token_type = $response_data1['token_type'];

    return $access_token;

}