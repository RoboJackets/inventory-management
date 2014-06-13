<?php

require 'Slim/Slim.php';
require 'php/render.php';
\Slim\Slim::registerAutoloader(); //Req'd since not using Composer


$app = new \Slim\Slim();


$app->get('/', function() {
    $app->render('views/htmlHeader.php', array('title'=>'Robojackets Inventory'));
});



$app->run();
