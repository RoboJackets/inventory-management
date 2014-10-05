<?php

$app->post('/add/validate-pn', function() use ($app) {
    // Set Database credentials
    if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
    require $path . 'c_Database.php';

    $connection = New Database();

    $count = array_shift($connection->searchQuery("SELECT COUNT(*) FROM parts WHERE part_num=(?)", $_POST['partNumber']));

    echo $count['COUNT(*)'];

});