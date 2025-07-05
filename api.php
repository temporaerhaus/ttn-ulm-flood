<?php
require 'vendor/autoload.php';

use TTNUlm\API;

$router = new AltoRouter();
$api = new API();

$router->map('GET', '/', function() use ($api) {
    echo 'root';
});

//**********
// Distance
//**********
$router->map('GET', '/distance/[i:id]/?', function($id) use ($api) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $api->returnDistance($id, $from, $to);
});

//**********
// State
//**********
$router->map('GET', '/state/[i:id]/?', function($id) use ($api) {
    $api->returnState($id);
});

//**********
// Sensors
//**********
$router->map('GET', '/sensors/?', function() use ($api) {
    $api->returnSensors();
});


// get matches
$match = $router->match();

// call closure or throw 404 status
if( is_array($match) && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
