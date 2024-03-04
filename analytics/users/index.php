<?php

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





$host_name = 'db5015402108.hosting-data.io';
$database = 'dbs12614573';
$user_name = 'dbu5199925';
$password = 'Pce@6ooAdH';

$conn = new mysqli($host_name, $user_name, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT * FROM users WHERE user_id = ?;";
$stmt = $conn->prepare($sql);
$id = $_GET['id'];
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if($result->num_rows <= 0){		//Usuario no existe en la BDD
	$nuevo_usuario = "INSERT INTO users (id, login, display_name, type, broadcaster_type, description, profile_image_url, offline_image_url, view_count, created_at) 
		VALUES (?,?,?,?,?,?,?,?,?,?);";
	$stmt2 = $conn->prepare($nuevo_usuario);
	$datos_usuario = consultaUsuario($id);
	
	$stmt2->bind_param("ssssssssis", $datos_usuario['id'], $datos_usuario['login'], $datos_usuario['display_name'], $datos_usuario['type'], $datos_usuario['broadcaster_type'], $datos_usuario['description'], $datos_usuario['profile_image_url'], $datos_usuario['offline_image_url'], $datos_usuario['view_count'], $datos_usuario['created_at']);
	$stmt2->execute();
	$stmt2->close();
}



function consultarUsuario($id){
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

		// Decodificar el JSON para formatearlo
	//$response2_decoded = json_decode($response2, true);

	// Convertir nuevamente a JSON con formato
	//$response2_encoded = json_encode($response2_decoded['data'], JSON_PRETTY_PRINT);

		
	//$cabecera = "Datos_usuario: ";
	//$datosConCabecera = $cabecera . $response2_encoded;
	header("Content-Type: application/json");
	$response2_encoded = json_encode($response2_decoded, JSON_PRETTY_PRINT);

	return $response2_encoded;
}

?>
