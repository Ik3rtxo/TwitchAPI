<?php

require_once('../topsofthetops/token.php');


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

$sql = "SELECT * FROM users WHERE user_id = ?;";
$stmt = $conn->prepare($sql);
$id = $_GET['id'];




$access_token = token();
// Inicializar cURL para la segunda consulta
$ch2 = curl_init();

$url = "https://api.twitch.tv/helix/users?id=". urlencode($id);
// Configurar la URL de la segunda solicitud
curl_setopt($ch2, CURLOPT_URL, $url);


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

$response2_decoded = json_decode($response2, true);

// Cerrar la conexión cURL de la segunda solicitud
curl_close($ch2);

$datos_usuario = json_encode($response2_decoded, JSON_PRETTY_PRINT);



$test_usuario = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($test_usuario);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if($result->num_rows <= 0){		//Usuario no existe en la BDD
	$nuevo_usuario = "INSERT INTO users (id, login, display_name, type, broadcaster_type, description, profile_image_url, offline_image_url, view_count, created_at) 
		VALUES (?,?,?,?,?,?,?,?,?,?);";
	$stmt2 = $conn->prepare($nuevo_usuario);
	$datos_usuario = json_decode(consultaUsuario($id, $access_token), true);

	$datos_usuario = $datos_usuario['data'][0];

	
	/*foreach($datos_usuario as $dato){
		echo "dato";
		echo json_encode($dato['login']);
	}*/
	
	$stmt2->bind_param("ssssssssis", $datos_usuario['id'], $datos_usuario['login'], $datos_usuario['display_name'], $datos_usuario['type'], $datos_usuario['broadcaster_type'], $datos_usuario['description'], $datos_usuario['profile_image_url'], $datos_usuario['offline_image_url'], $datos_usuario['view_count'], $datos_usuario['created_at']);
	$stmt2->execute();
	$stmt2->close();

	echo json_encode($datos_usuario);
}else{
	header("Content-Type: application/json");
	$respuesta = json_encode($result->fetch_assoc(), JSON_PRETTY_PRINT);
	echo $respuesta;
}
$conn.close();

function consultaUsuario($id, $access_token){
	$client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';
	// Inicializar cURL para la segunda consulta
	$ch2 = curl_init();

	$url = "https://api.twitch.tv/helix/users?id=". urlencode($id);
	// Configurar la URL de la segunda solicitud
	curl_setopt($ch2, CURLOPT_URL, $url);

	// Especificar que queremos realizar una solicitud GET
	//curl_setopt($ch2, CURLOPT_HTTPGET, true);

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

	$response2_decoded = json_decode($response2, true);

	// Cerrar la conexión cURL de la segunda solicitud
	curl_close($ch2);


	header("Content-Type: application/json");
	$response2_encoded = json_encode($response2_decoded, JSON_PRETTY_PRINT);
	return $response2_encoded;

}




?>
