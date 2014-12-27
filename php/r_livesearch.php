<?php

$app->get('/livesearch/:field', function($field) use ($app) {

    require 'c_Database.php';

    //Limits this to only approved column in the DB
    switch($field) {
        case 'part_num':
            $column = $field;
            $table = 'parts';
            break;
        case 'attribute':
            $column = $field;
            $table = 'attributes';
            break;
        case 'location':
            $column = $field;
            $table = 'locations';
            break;
        default:
            $app->response->setStatus(403); //If not allowed return an http error
    }
    
    
    $input = (string)$_GET['q'];

    $conn = new Database();
    $results = array();

    if (strlen($input) > 0) { //Only run query if it has length
        $results = $conn->searchQuery('SELECT (?), name FROM ' . $table . ' WHERE part_num LIKE (?) LIMIT 10', $column, "%" . $input . "%");
    }

    $return = array();
    foreach ($results as $index => $part_num) {
        $return[]['part'] = $part_num['part_num'];
    }
    
    $app->response->headers->set('Content-Type', 'application/json');
    $return = json_encode($return);
    echo $return;
    $conn->closeConnection(); // Close DB connection
});