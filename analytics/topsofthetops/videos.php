<?php

function videos($access_token, $juego){

    $ch3 = curl_init();

    $url3 = "https://api.twitch.tv/helix/videos?game_id=" . urlencode($juego['id']) . "&sort=views&first=40";
        
    // Configurar la URL de la segunda solicitud
    curl_setopt($ch3, CURLOPT_URL, $url3);

    // Especificar que queremos realizar una solicitud GET
    curl_setopt($ch3, CURLOPT_HTTPGET, true);

    $client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';

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

    curl_close($ch3);

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

    return $result[0];

}

?>

