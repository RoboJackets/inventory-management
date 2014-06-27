<?php

/* 
 * The script that runs when a search is requested from the client using ajax
 */

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; } // make sure path is known
if (file_exists($path . 'api.php')) { require_once $path . 'api.php'; } // include api functions

if ($_GET['input']) {   // begin searching if user input is given
    $connection = Connect();
    
    // function that searches the database and returns the results in an array
    $results = SearchDB($connection, $_GET['mode'], $_GET['input']);
    echo $results;
    
    mysqli_close($connection);  // close connection
}
