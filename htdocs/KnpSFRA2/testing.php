<?php

require __DIR__.'/vendor/autoload.php';
$client = new \GuzzleHttp\Client([
    'base_url' => 'http://localhost:8000',
    'defaults' => [
        'exceptions' => false
    ]
]);

$nickname = 'ObjectOrienter'.rand(0, 999);
$data = array(
    'nickname' => $nickname,
    'avatarNumber' => 5,
    'tagLine' => 'a test dev!'
);

$response = $client->post('/api/programmers', [
    'body' => json_encode($data)
]);

$programmerUrl = $response->getHeader('Location');

// 2) GET a programmer resource
$response = $client->get($programmerUrl);

// 3) GET a programmers resource
$response = $client->get('/api/programmers');

echo $response;
echo "\n\n";