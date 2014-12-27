<?php


}$app->get('/livesearch/:field', function () use ($app) {

    require 'c_Database.php';

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
            $app->response->setStatus(403);
    }
    
    
    $input = (string)$_GET['q'];

    $conn = new Database();
    $results = array();

    if (strlen($input) > 0) {
        $results = $conn->searchQuery('SELECT (?), name FROM (?) WHERE part_num LIKE (?) LIMIT 10', $column, $table, '%' . $input . '%');
    }

    $return = array();
    foreach ($results as $index => $part_num) {
        $return[]['part'] = $part_num['part_num'];
        //$return[]['name'] = $part_num['name'];
    }
    
    $app->response->headers->set('Content-Type', 'application/json');
    $return = json_encode($return);
    echo $return;
    $conn->closeConnection();
});