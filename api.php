<?php
require 'vendor/autoload.php';
use TTNUlm\API;
use TTNUlm\Flood;

$router = new AltoRouter();
$api = new API();

$router->map('GET', '/', function() use ($api) {
    echo 'root';
});

$router->map('GET', '/distance', function() use ($api) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $api->returnDistance($from, $to);
});
$router->map('GET', '/state', function() use ($api) {
    $api->returnState();
});


$match = $router->match();
if ($match) {
    $match['target']();
}

