<?php
// Initalizes the database connection. Include for all files that access db.

// Include configuration file
if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }                               
if (file_exists($path . 'config.php')) { require $path . 'config.php'; }   // include initial login info
else { exit(); };

// Create connection (object oriented way)
$CONN = new mysqli(HOST, USER, PASSWORD, DATABASE);

if ($CONN->connect_error)
    echo "Database connection failed: " . $CONN->connect_error, E_USER_ERROR . "\n";