<?php

use mysqli;

function token()
{

    $host_name = 'db5015402108.hosting-data.io';
    $database = 'dbs12614573';
    $user_name = 'dbu5199925';
    $password = 'Pce@6ooAdH';

    $conn = new mysqli($host_name, $user_name, $password, $database);

    $stmt = $conn->prepare("SELECT * FROM tokens");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $linea = $result->fetch_assoc();
        return $linea['token'];
    }

    $client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';
    $client_secret = 've1fjp9t5n3eygo0vu6igoxg6mj0i6';

    $post_data = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'client_credentials'
    );

    $ch1 = curl_init();
    curl_setopt($ch1, CURLOPT_URL, 'https://id.twitch.tv/oauth2/token');
    curl_setopt($ch1, CURLOPT_POST, 1);
    curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);

    curl_setopt(
        $ch1,
        CURLOPT_HTTPHEADER,
        array(
        'Content-Type: application/x-www-form-urlencoded'
        )
    );

    $response1 = curl_exec($ch1);

    if (curl_errno($ch1)) {
        echo 'Error: ' . curl_error($ch1);
    }

    curl_close($ch1);

    $response_data1 = json_decode($response1, true);
    $access_token = $response_data1['access_token'];

    $stmt2 = $conn->prepare("insert into tokens (user_id, token) values (?,?)");
    $stmt2->bind_param("ss", $client_id, $access_token);
    $stmt2->execute();

    return $access_token;
}
