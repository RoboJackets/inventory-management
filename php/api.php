<?php

/* 
 * This file contains the functions for making a connection to the database
 * and searching the database for information.
 */

// make sure required file(s) are set
if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
if (file_exists($path . 'db-conn.php')) { require_once $path . 'db-conn.php'; }
// =========================================

function SearchDB($mode, $search_input) {
    
    if ($mode == 'bin') { $sql_query = SearchByBin($search_input); } 
    else { $sql_query = SearchByPartNum($search_input); }  // default to barcode search - by partnum for test dev
    
    $json_results = FilterResults( mysqli_query($CONN, $sql_query) );   // main operations here
    
    return $json_results;    
}  
    
function FilterResults($result) {
    
    $response = array();
    if ($result->num_rows) {  // If results are found...
        while($row = mysqli_fetch_array($result)) {
            $temp['PartNum'] = $row['PART_NUM'];
            $temp['PartName'] = $row['name'];
            $temp['PartCat'] = $row['category'];
            $temp['PartLocation'] = $row['location'];
            $temp['PartAttrib'] = $row['attributes'];
            $temp['PartVal'] = $row['value'];

            // place the data into array of json data
            array_push($response, $temp);
        }
    } else {
        $temp['PartNum'] = 0000000000;
        $temp['PartName'] = "Not Found";
        $temp['PartCat'] = "";
        $temp['PartLocation'] = 000;
        $temp['PartAttrib'] = "N/A";
        $temp['PartVal'] = "N/A";
        array_push($response, $temp);
    }
    
    return json_encode($response);  // return JSON encoded data
}

function SearchByBarcode($barcode) {
    
    $query = "SELECT parts.PART_NUM, parts.name, parts.category, parts.location, attributes.attribute, attributes.value
    FROM barcode_lookup
    RIGHT JOIN parts
    ON barcode_lookup.PART_NUM=parts.PART_NUM
    RIGHT JOIN attributes
    ON barcode_lookup.PART_NUM=attributes.PART_NUM
    WHERE barcode_lookup.barcode=" . $barcode;
            
    return $query;
}

function SearchByPartNum($part_number) {
   
    $query = 'SELECT PART_NUM, name, category, location, attribute, value
    FROM parts
    JOIN attributes
    ON parts.PART_NUM=attributes.PART_NUM
    WHERE parts.PART_NUM=' . $part_number;
            
    return $query;
}

function SearchByBin($bin) {
    
    $query = "SELECT parts.PART_NUM, parts.name, parts.category, parts.location, attributes.attribute, attributes.value
    FROM parts
    JOIN attributes
    ON parts.PART_NUM=attributes.PART_NUM
    WHERE parts.location=" . $bin;
            
    return $query;
}

function StartSession() {       // function used for making initial connections
    
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;   // defined in rj-inv_config.php
    
    // Stop JavaScript from being able to access the session id.
    $httponly = true;
    
    // Forces sessions to only use cookies. [not really sure about the header here]
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header('Location: ../error.php?err=Could not initiate a safe session (ini_set)');
        exit();
    }
    
    // Get current cookies params.
    $cookieParams = session_get_cookie_params();
    
    // Set the cookie params.
    session_set_cookie_params(
        $cookieParams['lifetime'],
        $cookieParams['path'], 
        $cookieParams['domain'], 
        $secure,
        $httponly
        );
    
    session_name($session_name);    // Sets the session name to the one set above.
    session_start();                // Start the PHP session 
    session_regenerate_id();        // Regenerated the session, delete the old one.
    return;
}