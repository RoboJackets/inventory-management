<?php
//  used to perform a search on the database. returns json data.

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; } // make sure path is known
require $path.'db-conn.php';
require $path.'api.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {   // begin searching if user input is given

    echo 'mode:' . $_GET['mode'] . '\n';
    echo 'search input:' . $_GET['input'] . '\n';
    echo '-------------------------\n\n';
    
    // function that searches the database and returns json array
    echo SearchDB($_GET['mode'], $_GET['input']);
    
    echo "before close\n";
    
    mysqli_close($CONN);  // close connection
    
    echo "end - closed connection\n";
}
