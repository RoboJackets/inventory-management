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
