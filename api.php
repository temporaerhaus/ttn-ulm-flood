<?php
require 'vendor/autoload.php';
$router = new AltoRouter();

$router->map( 'GET', '/', function() {

});

// TODO finish this... :)

require __DIR__ . '/Flood.class.php';
Flood::create()->isFlood();

