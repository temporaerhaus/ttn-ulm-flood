<?php
use TTNUlm\Flood;
require 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => 'tmp/twig',
    'auto_reload' => true,
]);
$router = new AltoRouter();

//**************
// Frontpage
//**************
$router->map('GET', '/', function() use ($twig) {
    $herdbruecke = Flood::create()->isFlood(1);
    $eisenbahnbruecke = Flood::create()->isFlood(2);
    echo $twig->render('index.html', [
        'highwater1' => $herdbruecke[0],
        'diff1' => $herdbruecke[1],
        'abs1' => $herdbruecke[2],
        'highwater2' => $eisenbahnbruecke[0],
        'diff2' => $eisenbahnbruecke[1],
        'abs2' => $eisenbahnbruecke[2]
    ]);
});

//**************
// API Doc
//**************
$router->map('GET', '/apidoc', function() use ($twig) {
    echo $twig->render('apidoc.html');
});

$router->map('GET', '/impressum', function() use ($twig) {
    echo $twig->render('impressum.html');
});


// Route matching
$match = $router->match();
if ($match) {
    $match['target']();
}




