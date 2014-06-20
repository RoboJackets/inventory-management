<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader(); //Req'd since not using Composer


$app = new \Slim\Slim();

$app->get('/)', function() use ($app) {
    $app->view();
    $app->render('html.php', array('title'=>'Robojackets Inventory',
                                   'mode'=>'barcode'));
});

$app->get('/:mode', function($mode) use ($app) {
    $app->view();
    $app->render('html.php', array('title'=>'Robojackets Inventory',
                                   'mode'=>$mode));
});


$app->run();
