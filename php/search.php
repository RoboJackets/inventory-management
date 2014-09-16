<?php
/*
 * Used to perform a search on the database. Returns (echo) json formatted data.
 */

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; } // make sure path is known
require $path.'db-conn.php';
require $path.'api.php';
require $path.'c_Part.php';
/*
if ($_SERVER["REQUEST_METHOD"] == "GET") {   // begin searching if user input is given
    
    // function that searches the database and returns json formatted results.
    // can be found in the api functions
    echo SearchDB($_GET['mode'], $_GET['input']);
    
    mysqli_close($CONN);  // close connection
}
*/


ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);



// testing some different methods of searching
if ($_SERVER["REQUEST_METHOD"] == "GET") {   // begin searching if user input is given
    
    $part = new Part($_GET['input']);
    
    $part->findPartID();
    $part->findAllBarcodes();
    $part->findPartInfo();
    $part->showResults();
    $part->showExample();
    
}