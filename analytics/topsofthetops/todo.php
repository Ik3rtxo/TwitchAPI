<?php

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

// Inicializar cURL para la segunda consulta
$ch2 = curl_init();

$url = "https://api.twitch.tv/helix/games/top";

// Configurar la URL de la segunda solicitud
curl_setopt($ch2, CURLOPT_URL, $url);

// Especificar que queremos realizar una solicitud GET
curl_setopt($ch2, CURLOPT_HTTPGET, true);

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

//Establecemos la cabecera
header("Content-Type: application/json");

// Convertir el array de resultados a JSON y mostrarlo
$response2_encoded = json_encode($response2_decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

//echo $response2_encoded;

$final_result = array(); // Crear un array vacío

$ch3 = curl_init();

foreach ($response2_decoded['data'] as $juego) {

    $url3 = "https://api.twitch.tv/helix/videos?game_id=" . urlencode($juego['id']) . "&sort=views&first=40";

    // Configurar la URL de la segunda solicitud
    curl_setopt($ch3, CURLOPT_URL, $url3);

    // Especificar que queremos realizar una solicitud GET
    curl_setopt($ch3, CURLOPT_HTTPGET, true);

    // Establecer encabezados para la segunda solicitud
    curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
        'Authorization: ' . 'Bearer ' . $access_token,
        'Client-Id: ' . $client_id
    ));

    // Indicar que queremos recibir una respuesta
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar la segunda solicitud y obtener la respuesta
    $response3 = curl_exec($ch3);

    // Verificar si hay errores en la segunda solicitud
    if(curl_errno($ch3)){
        echo 'Error: ' . curl_error($ch3);
    }

    $response3_decoded = json_decode($response3, true);

    //$response3_decoded['data']['game_id'] = $juego['id'];
    //$response3_decoded['data']['game_name'] = $juego['name'];

    foreach ($response3_decoded['data'] as &$videodata) {
        // Aquí puedes definir los valores de los nuevos atributos como desees
        // Por ejemplo, asignar valores ficticios
        $videodata['game_id'] = $juego['id']; // Asigna un ID de juego aleatorio
        $videodata['game_name'] = $juego['name']; // Asigna un nombre de juego ficticio
    }

    $data = $response3_decoded;

    // Array para almacenar la información requerida
    $result = array();

    // Iterar sobre los datos para obtener la información requerida
    foreach ($data['data'] as $video) {
        $game_id = $video['game_id'];
        $game_name = $video['game_name'];
        $user_name = $video['user_name'];
        $view_count = $video['view_count'];
        $duration = $video['duration'];
        $created_at = $video['created_at'];

        // Si es el primer video del usuario, inicializa sus datos
        if (!isset($result[$user_name])) {
            $result[$user_name] = array(
                'game_id' => $game_id,
                'game_name' => $game_name,
                'user_name' => $user_name,
                'total_videos' => 1,
                'total_views' => $view_count,
                'most_viewed_title' => $video['title'],
                'most_viewed_views' => $view_count,
                'most_viewed_duration' => $duration,
                'most_viewed_created_at' => $created_at
            );
        } else {
            // Actualizar los datos del usuario
            $result[$user_name]['total_videos']++;
            $result[$user_name]['total_views'] += $view_count;

            // Actualizar el video más visto si es necesario
            if ($view_count > $result[$user_name]['most_viewed_views']) {
                $result[$user_name]['most_viewed_title'] = $video['title'];
                $result[$user_name]['most_viewed_views'] = $view_count;
                $result[$user_name]['most_viewed_duration'] = $duration;
                $result[$user_name]['most_viewed_created_at'] = $created_at;
            }
        }
    }

    $result = array_values($result);

    array_push($final_result, $result[0]);

    //echo json_encode($result, JSON_PRETTY_PRINT);

}

curl_close($ch3);

$final_result_encoded = json_encode($final_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo $final_result_encoded;

?>

