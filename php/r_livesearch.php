<?php

$app->get('/livesearch/:field', function ($field) use ($app) {

    require 'c_Database.php';

    //Limits this to only approved column in the DB
    switch ($field) {
        case 'pn':
            $table = 'parts';
            $column = 'part_num';
            $selections = 'part_num, name';
            break;

        case 'atr':
            $table = 'attributes';
            $column = 'attribute';
            $selections = 'attribute, value';
            break;

        case 'loc':
            $table = 'locations';
            $column = 'location';
            $selections = 'location';
            break;

        default:
            echo "<h2>No <i>livesearch</i> API call for <i>" . $field . '</i></h2>';
            $app->response->setStatus(403); // If not allowed return an http error
            return;
    }

    $conn = new Database();
    $results = array(); // array for storing the found results
    $input = '%' . $_GET['q'] . '%';    // fixup the user's input for the query

    // Only run query if there is at least one character to search for after fixing up the input
    if (strlen($input) > 2) {
        $params = array($input);    // must pass array of values as a parameter to `searchQuery` method
        $results = $conn->searchQuery('SELECT ' . $selections . ' FROM ' . $table . ' WHERE ' . $column . ' LIKE (?) LIMIT 10', $params);
    }

    if ($results > 0) {
        $data = array();
        foreach ($results as $index => $val) {
            foreach ($val as $index2 => $val2) {
                $data[][$index2] = $val2;
            }
        }

        $conn->closeConnection(); // Close DB connection

        if (!$data) {
            $data = 0;
        }

        $app->response->headers->set('Content-Type', 'application/json');
        $data = json_encode($data);
        echo $data;
    } else {
        $app->response->setStatus(500); // Something is wrong with the code
    }
});