<?php

require "videos.php";
require "top3.php";
require "token.php";

$token = token();

$top3_juegos = top3($token);

$host_name = 'db5015402108.hosting-data.io';
$database = 'dbs12614573';
$user_name = 'dbu5199925';
$password = 'Pce@6ooAdH';

$conn = new mysqli($host_name, $user_name, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['since'])) {
    $tiempo = $_GET['since'];
} else {
    $tiempo = 600;
}

$sql = "SELECT * FROM topsofthetops";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$resultado_final = array();

if ($result->num_rows > 0) {
    $cumplen = array();
    $ids_toda_la_base = array();
    while ($linea = $result->fetch_assoc()) {
        $fecha1 = new DateTime($linea['fecha']);
        $fecha2 = new DateTime();
        $diferencia = $fecha1->diff($fecha2);
        $minutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;

        if ($minutos < $tiempo / 60) {
            unset($linea['fecha']);
            $cumplen[] = $linea;
        }

        $ids_toda_la_base[] = $linea['game_id'];
    }
    foreach ($top3_juegos['data'] as $juego) {
        $booleano = true;
        foreach ($cumplen as $cumplen_base) {
            if ($juego['id'] == $cumplen_base['game_id']) {
                $resultado_final[] = $cumplen_base;
                $booleano = false;
                //echo "Juego encontrado en los datos que cumplen la medida de tiempo";
            }
        }
        if ($booleano) {
            $videos_juego_no_encontrado = videos($token, $juego);
            $resultado_final[] = $videos_juego_no_encontrado;
            $fecha = date('Y-m-d H:i:s');
            if (in_array($juego['id'], $ids_toda_la_base)) {
                $nuevo_juego_sql = "UPDATE topsofthetops 
                SET user_name = ?, total_videos = ?, total_views = ?, most_viewed_title = ?, 
                most_viewed_views = ?, most_viewed_duration = ?, most_viewed_created_at = ?, fecha = ? 
                WHERE game_id = ?";
                $stmt3 = $conn->prepare($nuevo_juego_sql);
                $stmt3->bind_param(
                    "siisissss",
                    $videos_juego_no_encontrado['user_name'],
                    $videos_juego_no_encontrado['total_videos'],
                    $videos_juego_no_encontrado['total_views'],
                    $videos_juego_no_encontrado['most_viewed_title'],
                    $videos_juego_no_encontrado['most_viewed_views'],
                    $videos_juego_no_encontrado['most_viewed_duration'],
                    $videos_juego_no_encontrado['most_viewed_created_at'],
                    $fecha,
                    $videos_juego_no_encontrado['game_id']
                );
                $stmt3->execute();
                $stmt3->close();
            } else {
                $insertar_nuevo = "INSERT INTO topsofthetops (game_id, game_name, user_name, total_videos, total_views, 
                most_viewed_title, most_viewed_views, most_viewed_duration, most_viewed_created_at, fecha) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt4 = $conn->prepare($insertar_nuevo);
                $stmt4->bind_param(
                    "sssiisisss",
                    $videos_juego_no_encontrado['game_id'],
                    $videos_juego_no_encontrado['game_name'],
                    $videos_juego_no_encontrado['user_name'],
                    $videos_juego_no_encontrado['total_videos'],
                    $videos_juego_no_encontrado['total_views'],
                    $videos_juego_no_encontrado['most_viewed_title'],
                    $videos_juego_no_encontrado['most_viewed_views'],
                    $videos_juego_no_encontrado['most_viewed_duration'],
                    $videos_juego_no_encontrado['most_viewed_created_at'],
                    $fecha
                );
                $stmt4->execute();
                $stmt4->close();
            }
        }
    }
} else {
    $fecha = date('Y-m-d H:i:s');
    foreach ($top3_juegos as $juego) {
        $videos_juego = videos($token, $juego);
        $resultado_final[] = $videos_juego;
        $insertar = "INSERT INTO topsofthetops VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($insertar);
        $stmt2->bind_param(
            "sssiisisss",
            $videos_juego['game_id'],
            $videos_juego['game_name'],
            $videos_juego['user_name'],
            $videos_juego['total_videos'],
            $videos_juego['total_views'],
            $videos_juego['most_viewed_title'],
            $videos_juego['most_viewed_views'],
            $videos_juego['most_viewed_duration'],
            $videos_juego['most_viewed_created_at'],
            $fecha
        );
        $stmt2->execute();
    }
    $stmt2->close();
}

$resultado_final_encodeado = json_encode($resultado_final, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

header("Content-Type: application/json");

echo $resultado_final_encodeado;

$stmt->close();
$conn->close();
