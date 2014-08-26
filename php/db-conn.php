<?php
// Initalizes the database connection. Include for all files that access db.

// Include configuration file
if (!isset($path)) { $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }                 
if (!defined('HOST')) { require $path . 'config.php'; }   // make sure constants are defined

// Create connection
$CONN = new mysqli(HOST, USER, PASSWORD, DATABASE);

// Check for errors
if ($CONN->connect_error) {
    echo "Database connection failed: " . $CONN->connect_error, E_USER_ERROR . "\n";
}