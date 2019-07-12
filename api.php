<?php
require 'vendor/autoload.php';
use TTNUlm\API;
use TTNUlm\Flood;

$router = new AltoRouter();

$api = new API();
$router->map('GET', '/api/distance', function() use ($api) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $api->returnDistance($from, $to);
});

// TODO finish this...
//$from = $_GET['from'];
//$to = $_GET['to'];
//$api->returnDistance($from, $to);
//
//$api->state();
//Flood::create()->isFlood();

