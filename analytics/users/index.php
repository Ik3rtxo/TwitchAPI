<?php

require '../topsofthetops/token.php';

$host_name = 'db5015402108.hosting-data.io';
$database = 'dbs12614573';
$user_name = 'dbu5199925';
$password = 'Pce@6ooAdH';

$client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';
$conn = new mysqli($host_name, $user_name, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
    echo "bdd muere";
}

$id_usuario = $_GET['id'];
$access_token = token();

$test_usuario = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($test_usuario);
$stmt->bind_param("s", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows <= 0) {
    $nuevo_usuario = "INSERT INTO users (id, login, display_name, type, 
    broadcaster_type, description, profile_image_url, offline_image_url, view_count, created_at) 
	VALUES (?,?,?,?,?,?,?,?,?,?);";
    $stmt2 = $conn->prepare($nuevo_usuario);

    $ch2 = curl_init();
    $url = "https://api.twitch.tv/helix/users?id=" . urlencode($id_usuario);
    curl_setopt($ch2, CURLOPT_URL, $url);
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
    $datos_usuario = $response2_decoded['data'][0];

    $stmt2->bind_param(
        "ssssssssis",
        $datos_usuario['id'],
        $datos_usuario['login'],
        $datos_usuario['display_name'],
        $datos_usuario['type'],
        $datos_usuario['broadcaster_type'],
        $datos_usuario['description'],
        $datos_usuario['profile_image_url'],
        $datos_usuario['offline_image_url'],
        $datos_usuario['view_count'],
        $datos_usuario['created_at']
    );
    $stmt2->execute();
    $stmt2->close();
    echo json_encode($datos_usuario);
} else {
    header("Content-Type: application/json");
    $respuesta = json_encode($result->fetch_assoc(), JSON_PRETTY_PRINT);
    echo $respuesta;
}
$conn->close();
