<?php

/* 
 * This is the PHP codes that is used to connect to the database. Include
 * this file with every page that connects to the database.
 */

// Include configuration file. Listing it without the full path names allows for server mobility.
// Ensure root path is known
if(!isset($path)){
    $path = $_SERVER['DOCUMENT_ROOT'].'/php/';                                
}

// Include the database configuration settings
include_once($path.'config.php');

// Create connection (object oriented way)
$conn = new mysqli(HOST, USER, PASSWORD, DATABASE);

// Check for connection error
if ($conn->connect_error) {
    trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}