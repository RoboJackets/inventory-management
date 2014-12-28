<?php

$app->get('/livesearch/:field', function ($field) use ($app) {

    require 'c_Database.php';

    //Limits this to only approved column in the DB
    switch ($field) {
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

    $input = '%';
    $input .= (string)$_GET['q'];
    $input .= '%';

    $conn = new Database();
    $results = array();

    if (strlen($input) > 0) { //Only run query if it has length | NOTE: TEMPORARY FIX IN PLACE HERE - FUTURE WORK NEEDS TO BE MADE FOR THIS
        // $results = $conn->searchQuery('SELECT part_num, name FROM ' . $table . ' WHERE part_num LIKE (?) LIMIT 10', '%' . $input . '%');
        //$params = array($table, $input);
        //$param_types = array('s', 's');
        $params = array($input);
        $results = $conn->searchQuery('SELECT ' . $column . ' FROM ' . $table . ' WHERE ' . $column . ' LIKE (?) LIMIT 10', $params);
    }

    $data = array();
    foreach ($results as $index => $val) {
        foreach ($val as $index2 => $val2) {
            $data[][$index2] = $val2;
        }
    }

    $app->response->headers->set('Content-Type', 'application/json');
    $data = json_encode($data);
    echo $data;
    $conn->closeConnection(); // Close DB connection
});