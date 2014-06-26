<?php

/* 
 * This file contains the functions for making a connection to the database
 * and searching the database for information.
 */

    if(!isset($path)){              // set path for php files
        $path = $_SERVER['DOCUMENT_ROOT'].'/php/';                                    
    }

    if (file_exists($path . 'db-conn.php')) {
        require_once $path . 'db-conn.php';       // include configuration file
    } else {
        // throw error
    }
    
// =========================================

function SearchDB($search_input, $mode) {
    
    if ($mode == 'barcode') {
        $sql_query = SearchByBarcode($search_input);
    } elseif ($mode == 'bin') {
        $sql_query = SearchByBin($search_input);
    } else {
        // something here
    }
    
    $results = mysqli_query($CONN, $sql_query);
    FilterResults($results);
    
    return;    
}  
    
function FilterResults($result) {
    
    $json_response = array();

    if ($result->num_rows) {  // If results are found...
        while($row = mysqli_fetch_array($result)) {
            $temp['PART_NUM'] = $row['PART_NUM'];
            $temp['name'] = $row['name'];
            $temp['category'] = $row['category'];
            $temp['location'] = $row['location'];
            $temp['attributes'] = $row['attributes'];
            $temp['value'] = $row['value'];

            // place the data into array of json data
            array_push($json_response, $temp);
        }
    } else {
        // part does not exist
    }   
    
    echo json_encode(json_response);
    return;
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
   
    $query = "SELECT parts.PART_NUM, parts.name, parts.category, parts.location, attributes.attribute, attributes.value
    FROM parts
    JOIN attributes
    ON parts.PART_NUM=attributes.PART_NUM
    WHERE parts.PART_NUM=" . $part_number;
            
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