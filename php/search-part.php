<?php

/* 
 * This script takes a part number given to it by the user and queries the
 * database for any matches.
 */

// Ensure root path is known
if(!isset($path)){
    $path = $_SERVER['DOCUMENT_ROOT'].'/php/';                                    
}
// Include the api functions
include_once($path.'api.php');

// Start session for mySQL server access
StartSession();

// Return error if connection can not be made
if (!$conn) {
  die('Could not connect: ' . mysqli_error($conn));
}

// Define the statement that will be used to query the database
$by_part = "SELECT * FROM parts WHERE PART_NUM = '".$barcode."'";

// Process the query and return the result(s)
$result = mysqli_query($conn, $by_part);

// Structure returned data into json element
while($row = mysqli_fetch_array($result)) {
    $temp['PART_NUM'] = $row['PART_NUM'];
    $temp['name'] = $row['name'];
    $temp['category'] = $row['category'];
    $temp['value'] = $row['value'];
    $temp['package'] = $row['package'];
    $temp['description'] = $row['description'];
    $temp['datasheet'] = $row['datasheet'];
    $temp['attributes'] = $row['attributes'];
    $temp['location'] = $row['location'];
    $temp['flag_error'] = $row['flag_error']; // what exactly is this?
    $temp['status'] = $row['status'];
    $temp['updated'] = $row['updated'];

    // place the data into array of json data
    array_push($json_response,$temp);
}

$part_number = $json_response[0][0];
$part_name = $json_response[0][1];
$bin_number = $json_response[0][5];

// use JSON function to encode data from query
// echo json_encode($json_response);

// Close the connection to the database
mysqli_close($conn);