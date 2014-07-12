<?php
// Initalizes the database connection. Include for all files that access db.

// Include configuration file
if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }                               
if (file_exists($path . 'config.php')) { require $path . 'config.php'; }   // include initial login info
else { exit(); };

// Create connection (object oriented way)
$GLOBALS['CONN'] = new mysqli(HOST, USER, PASSWORD, DATABASE);

if ( $GLOBALS['CONN']->connect_error)
    trigger_error('Database connection failed: ' . $GLOBALS['CONN']->connect_error, E_USER_ERROR);