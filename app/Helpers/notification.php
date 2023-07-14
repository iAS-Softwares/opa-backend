<?php

function sendExpoPushNotification($token, $title, $body) {
    $url = env('EXPO_PUSH_NOTIFICATION_URL');
    
    $data = [
        'to' => $token,
        'title' => $title,
        'body' => $body,
        'sound' => 'default',
    ];
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        echo 'Failed to send Expo push notification: ' . curl_error($ch);
    } else {
        $responseData = json_decode($response, true);
        if (isset($responseData['errors'])) {
            echo 'Failed to send Expo push notification: ' . $responseData['errors'][0]['message'];
        } else {
            echo 'Expo push notification sent successfully!';
        }
    }
    
    curl_close($ch);
}