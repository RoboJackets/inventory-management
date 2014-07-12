<?php
//  used to perform a search on the database. returns json data.

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; } // make sure path is known
require $path.'api.php';
require $path.'db-conn.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {   // begin searching if user input is given
    
    // function that searches the database and returns json array
    echo SearchDB($_GET['mode'], $_GET['input']);
    
    mysqli_close($CONN);  // close connection
}