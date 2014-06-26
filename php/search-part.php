<?php

/* 
 * This script takes a part number given to it by the user and queries the
 * database for any matches.
 */

// Ensure root path is known
if(!isset($path)){
    $path = $_SERVER['DOCUMENT_ROOT'].'/php/';                                    
}

if (file_exists($path . 'api.php')) {
    require_once $path . 'api.php';       // include configuration file
} else {
    // throw error
}

// Start session for mySQL server access
StartSession();

SearchDB($user_search_input);

// Close the connection to the database
mysqli_close($conn);