<?php

/* 
 * This script takes a part number given to it by the user and queries the
 * database for any matches.
 */

// Include the api functions and connection
include_once 'rj-inv_api.php';
include_once 'rj-inc_db-conn.php';
        
// Get request
$app = new \Slim\Slim();
$app->get('/search/:pn', function($pn) {
    

// Start session for mySQL server access
start_session();

// Return error if connection can not be made
if (!$conn) {
  die('Could not connect: ' . mysqli_error($conn));
}

// Define the statement that will be used to query the database
$by_part = "SELECT * FROM parts WHERE PART_NUM = '".$pn."'";

// Process the query and return the result(s)
$result = mysqli_query($conn,$by_part);

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

// use JSON function to encode data from query
echo json_encode($json_response);

// Close the connection to the database
mysqli_close($conn);

});