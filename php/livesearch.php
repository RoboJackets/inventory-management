<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/15/2014
 * Time: 12:39 AM
 */

$app->get('/livesearch', function () {

    if (!isset($path)) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
    }
    require $path . 'c_Database.php';

    $input = (string)$_GET['q'];

    $db = new Database();
    $results = array();

    if (strlen($input) > 0) {
        $results = $db->searchQuery('SELECT part_num FROM parts WHERE part_num LIKE (?) LIMIT 10', '%' . $input . '%');
    }

    $return = array();
    foreach($results as $index => $part_num) {
        $return[]['part'] = $part_num['part_num'];
    }

    $return = json_encode($return);
    echo $return;
});