<?php
require 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => 'tmp/twig',
    'auto_reload' => true,
]);

require __DIR__ . '/Flood.class.php';
$res = Flood::create()->isFlood();

echo $twig->render('index.html', [
    'name' => 'Fabien',
    'highwater' => $res[0],
    'diff' => $res[1]
]);
