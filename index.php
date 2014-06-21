<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader(); //Req'd since not using Composer

$app = new \Slim\Slim();

$app->get('/', function() use ($app) {
    $app->view();
    $app->render('html.php', array('title'=>'Robojackets Inventory',
                                   'mode'=>'barcode'));
});

$app->get('/:mode', function($mode) use ($app) {
    $app->view();
    $app->render('html.php', array('title'      =>  'Robojackets Inventory',
                                   'mode'       =>  $mode,
                                    ));
});

$app->get('/barcode/:barcode', function($barcode) use ($app) {
    $app->view();
    $app->render('html.php', array( 'title'     =>  'Robojackets Inventory',
                                    'subtitle'  =>  'Barcode',
                                    'mode'      =>  'barcode',
                                    'barcode'   =>  $barcode
                                    ));
});

$app->get('/bin/:bin', function($bin) use ($app) {
    $app->view();
    $app->render('html.php', array( 'title'     =>  'Robojackets Inventory',
                                    'subtitle'  =>  'Bin',
                                    'mode'      =>  'bin',
                                    'bin'       =>  $bin
                                    ));
});

$app->run();
