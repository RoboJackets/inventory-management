<?php
/*
 * Used to perform a search on the database. Returns (echo) json formatted data.
 */

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; } // make sure path is known
require $path.'c_MultiPart.php';


// begin searching if user input is given
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    
    $mode = $_GET['mode'];

    $conn = new Database();

    switch($mode)
    {
        case 'barcode':
            $part = new Part($conn, $_GET['input']);
            $part->findPart();
            $part->sendPart();
            break;
        case 'bin':
            $bin = new MultiPart($conn, $_GET['input']);
            $bin->findBin();
            $bin->sendBin();
            break;
        default:
            // do nothing
    }

}