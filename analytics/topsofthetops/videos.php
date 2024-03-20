<?php

function videos($access_token, $juego)
{

    $ch3 = curl_init();

    $url3 = "https://api.twitch.tv/helix/videos?game_id=" . urlencode($juego['id']) . "&sort=views&first=40";

    curl_setopt($ch3, CURLOPT_URL, $url3);
    curl_setopt($ch3, CURLOPT_HTTPGET, true);

    $client_id = 'f1uk5seih48k4fodvx7dy5mx2obo46';

    curl_setopt(
        $ch3,
        CURLOPT_HTTPHEADER,
        array(
        'Authorization: ' . 'Bearer ' . $access_token,
        'Client-Id: ' . $client_id
        )
    );

    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);

    $response3 = curl_exec($ch3);

    if (curl_errno($ch3)) {
        echo 'Error: ' . curl_error($ch3);
    }

    curl_close($ch3);

    $response3_decoded = json_decode($response3, true);

    foreach ($response3_decoded['data'] as &$videodata) {
        $videodata['game_id'] = $juego['id'];
        $videodata['game_name'] = $juego['name'];
    }

    $data = $response3_decoded;

    $result = array();

    foreach ($data['data'] as $video) {
        $game_id = $video['game_id'];
        $game_name = $video['game_name'];
        $user_name = $video['user_name'];
        $view_count = $video['view_count'];
        $duration = $video['duration'];
        $created_at = $video['created_at'];

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
            $result[$user_name]['total_videos']++;
            $result[$user_name]['total_views'] += $view_count;
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
