<?php
require "vendor/autoload.php";
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
$data = json_decode(file_get_contents("php://input"));
$text = $data->text;
$client = new Client("AIzaSyC34WQ4O-GX-GecTabr0cMLDcHTqLDBn4U");
$response = $client->geminiPro()->generateContent(
    new TextPart($text),
);
echo $response->text();