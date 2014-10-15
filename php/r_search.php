<?php

$app->get('/search', function () {

    require 'c_MultiPart.php';

    $mode = $_GET['mode'];
    $conn = New Database();
    switch ($mode) {
        case 'barcode':
            $part = New Part($conn, array('barcode' => $_GET['input']));

            // if no part was found by the barcode, assume user input was a part number and try again
            if (!$part->in_db) {
                $part = New Part($conn, array('part_num' => $_GET['input']));
            }

            $part->sendPart();
            break;
        case 'bin':
            $bin = New MultiPart($conn, $_GET['input']);
            $bin->findBin();
            $bin->sendBin();
            break;
        default:
            // do nothing
    }
    $conn->closeConnection();
});

$app->get('/livesearch', function () {

    require 'c_Database.php';

    $input = (string)$_GET['q'];

    $db = new Database();
    $results = array();

    if (strlen($input) > 0) {
        $results = $db->searchQuery('SELECT part_num, name FROM parts WHERE part_num LIKE (?) LIMIT 10', '%' . $input . '%');
    }

    $return = array();
    foreach ($results as $index => $part_num) {
        $return[]['part'] = $part_num['part_num'];
        //$return[]['name'] = $part_num['name'];
    }

    $return = json_encode($return);
    echo $return;
});