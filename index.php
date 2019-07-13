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
    $res = Flood::create()->isFlood();
    echo $twig->render('index.html', [
        'highwater' => $res[0],
        'diff' => $res[1]
    ]);
});

//**************
// API Doc
//**************
$router->map('GET', '/apidoc', function() use ($twig) {
    echo $twig->render('apidoc.html');
});


// Route matching
$match = $router->match();
if ($match) {
    $match['target']();
}




