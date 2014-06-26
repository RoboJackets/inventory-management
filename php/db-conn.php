<?php

/* 
 * This is the PHP codes that is used to connect to the database. Include
 * this file with every page that connects to the database.
 */

// Include configuration file. Listing it without the full path names allows for server mobility.
if(!isset($path)){
    $path = $_SERVER['DOCUMENT_ROOT'].'/php/';                                
}

if (file_exists($path . 'config.php')) {
    require_once $path . 'config.php';       // include initial login info
} else {
    // throw error
}

// Create connection (object oriented way)
$CONN = new mysqli(HOST, USER, PASSWORD, DATABASE);

// Check for connection error
if ($CONN->connect_error) {
    trigger_error('Database connection failed: ' . $CONN->connect_error, E_USER_ERROR);
}