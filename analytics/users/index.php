<?php
require_once('../topsofthetops/token.php');

// Extraer el token de acceso de la primera respuesta
$access_token = token();
//$token_type = $response_data1['token_type'];


// Conexión a la base de datos (asumiendo que ya tienes los detalles de conexión configurados)
$host_name = 'db5015402108.hosting-data.io';
$database = 'dbs12614573';
$user_name = 'dbu5199925';
$password = 'Pce@6ooAdH';

// Crear conexión
$conn = new mysqli($host_name, $user_name, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
if($result->num_rows>0){
	//echo "llegue donde tenia que llegar";
	/*header("Content-Type: application/json");
	$respuesta = json_encode($result->fetch_assoc(), JSON_PRETTY_PRINT);
	echo $respuesta;*/
}else{
	echo "El usuario con id: ".$id." no esta en la BD";
	echo "<br> Trabajo de Iker";
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
	// Cerrar la conexión cURL de la segunda solicitud
	curl_close($ch2);
	header("Content-Type: application/json");
	$response2_encoded = json_encode($response2, JSON_PRETTY_PRINT);

	echo $response2_encoded;
}
$conn->clase();
?>
