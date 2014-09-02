<?php
/* 
 * Contains all functions and subroutines used for accessing the db.
 */

// ensure that an open connection can be used to access the database
require $path.'db-conn.php';

/*
 * Pass the search mode and search_input into this function and the rest is 
 * taken care of. Uses prepared statements to prevent database injection
 */
function SearchDB($mode, $input) {
    $placeholder = new stdClass();
    $partData = new stdClass();

    $part_id = getPartID($input);

    $placeholder->parts = getPartInfo($part_id);
    
    foreach($placeholder->parts as $key => $val) { // itterate through all fields
            $partData->$key = $val; 
    }
     
    $partData->barcodes = getAllBarcodes($partData->part_id);
    $partData->attributes = getAttributes($partData->part_id);
    unset($partData->part_id);
    
    var_dump($partData);
    echo "-------------------------- \n";
    temp();
    
    //$sql_statement = sql_Barcode();
    //$results = queryDB($sql_statement, $input);
    //return $results;
}   //  ==========  SearchDB ==========


function getPartID($barcode) {
    $results = FilterResults(queryDB("SELECT * FROM barcode_lookup WHERE barcode=(?)", $barcode));
    return $results[0]->part_id;
}

function getAllBarcodes($part_id) {
    $result = FilterBarcodes(queryDB("SELECT barcode FROM barcode_lookup WHERE part_id=(?)", $part_id));
    return $result;
    
}

function getAttributes($part_id) {
    return FilterResults(queryDB("SELECT attribute, value, priority FROM attributes WHERE part_id=(?)", $part_id));
}

function getPartInfo($part_id) {
    $result = FilterResults(queryDB("SELECT * FROM parts WHERE part_id=(?)", $part_id));
    return $result[0];
}

function queryDB($sql, $input) {
    global $CONN;   // let function know about the global declared connection

    if(!$query = $CONN->prepare($sql)){
        echo "Error: Could not prepare query statement. (" . $query->errno . ") " . $query->error . "\n";
    }
    if (!$query->bind_param("s", $input)) {
        echo "Error: Failed to bind parameters to statement. (" . $query->errno . ") " . $query->error . "\n";
    }
    if (!$query->execute()) {
        echo "Error: Failed to execute query. (" . $query->errno . ") " . $query->error . "\n";
    }
    return $query;   // return the results after formatting to an arry of php objects
}

/*
 * This function filters the results from the searched data and formats it as
 * json encoded information that is returned to the caller.
 */
function FilterResults($query) {
    $meta = $query->result_metadata();  // get the metadata from the results
    
    // store the field heading names into an array, pass by reference
    while ($field = $meta->fetch_field()) {
        $params[] = &$row[$field->name];
    }

    // callback function; same as: $query->bind_result($params)
    call_user_func_array(array($query, 'bind_result'), $params);

    while ($query->fetch()) {   // fetch the results for every field
        
        $tmpObj = new stdClass();
        
        foreach($row as $key => $val) { // itterate through all fields
            $tmpObj->$key = $val; 
        }

        // add row (now as object) to the array of results
        $results[] = $tmpObj;
    }

    // close the open database/query information
    $meta->close();
    $query->close();
    
    // format the info as json data and return
    return $results;
}

function FilterBarcodes($query) {
    $meta = $query->result_metadata();  // get the metadata from the results
    
    // store the field heading names into an array, pass by reference
    while ($field = $meta->fetch_field()) {
        $params[] = &$row[$field->name];
    }

    // callback function; same as: $query->bind_result($params)
    call_user_func_array(array($query, 'bind_result'), $params);

    while ($query->fetch()) {   // fetch the results for every field
        /*
        foreach($row as $key => $val) { // itterate through all fields
            $tmpObj[] = $val; 
        }*/

        // add row (now as object) to the array of results
        $barcodes[] = $row['barcode'];
    }

    // close the open database/query information
    $meta->close();
    $query->close();
    
    // format the info as json data and return
    return $barcodes;
}


function sql_Barcodes() { // query part information from a barcde
    return "SELECT * FROM barcode_lookup WHERE part_id=(?)";
}   //  ==========  sql_Barcode  ==========

function sql_Part_ID(){
    return "SELECT * FROM barcode_lookup WHERE barcode=(?)";
}


function temp() {
    var_dump( json_decode('{"parts":[
    {"part_num":"11593lgy",
    "name":"My Cool Part",
    "category":"ic",
    "description":"A really cool part",
    "datasheet":"www.sketchywebsite.com/datasheet.pdf",
    "location":"A04",
    "barcodes":["200541","3011826"],
    "attributes":[
        {"attribute":"Package",
        "value":"SOIC8",
        "priority":"2"
        },
        {"attribute":"Voltage",
        "value":"6v",
        "priority":"4"
        }]
    },
    {"part_num":"14dgfy6",
    "name":"My 2nd Cooler Part",
    "category":"resistor",
    "description":"My secod part. It Exists only in JSON",
    "datasheet":"www.legitwebsite.com/datasheet2.pdf",
    "location":"B06",
    "barcodes":["2230531","5389381"],
    "attributes":[
        {"attribute":"Package",
        "value":"SOIC10",
        "priority":"1"
        },
        {"attribute":"Voltage",
        "value":"12v",
        "priority":"3"
        }]
    }
]
}'));
return;
}


function sql_Part() {    // query part information from a part number
    return "SELECT barcode AS PackageIDs, "
            . "parts.PART_NUM AS PartNum, "
            . "barcode_lookup.added AS BarAdd, "
            . "name AS PartName, category AS PartCat, "
            . "description AS PartDesc, "
            . "datasheet AS PartSheet, "
            . "location AS PartLocation, "
            . "flag_error AS PartErr, "
            . "status AS PartStatus, "
            . "parts.updated AS PartUpdated, "
            . "GROUP_CONCAT(attributes.attribute) AS AtribKeys, "
            . "GROUP_CONCAT(attributes.value) AS AtribVals "
            . "FROM parts "
                . "LEFT JOIN barcode_lookup "
                . "ON parts.PART_NUM=barcode_lookup.PART_NUM "
                . "LEFT JOIN attributes "
                . "ON attributes.PART_NUM=parts.PART_NUM "
            . "WHERE parts.PART_NUM=(?)";
}   //  ==========  sql_Part    ==========

function sql_Bin() {    // query part information from a bin number
    return "SELECT barcode AS PackageIDs, "
        . "parts.PART_NUM AS PartNum, "
        . "barcode_lookup.added AS BarAdd, "
        . "name AS PartName, "
        . "category AS PartCat, "
        . "description AS PartDesc, "
        . "datasheet AS PartSheet, "
        . "location AS PartLocation, "
        . "flag_error AS PartErr, "
        . "status AS PartStatus, "
        . "parts.updated AS PartUpdated, "
        . "FROM parts "
            . "LEFT JOIN barcode_lookup "
            . "ON parts.PART_NUM=barcode_lookup.PART_NUM "
        . "WHERE parts.location=(?)";
}   //  ==========  sql_Bin ==========


function sql_NumBarcodes(){  // the total number of unique barcodes
    return "SELECT COUNT(*) FROM barcode_lookup";
}

function sql_NumParts(){    // the total number of unique parts(components)
    return "SELECT COUNT(*) FROM parts";
}

function sql_NumPackages(){   // find how many packages of a given barcode exist
    return "SELECT COUNT(*) FROM barcode_lookup"
        . "LEFT JOIN barcode_lookup ON barcode_lookup.PART_NUM=parts.PART_NUM"
        . "WHERE barcode_lookup.barcode=(?)";
}

function sql_AddAttribs() {
    return "INSERT INTO attributes"
        . "(PART_NUM, attribute, value, priority)"
        . "VALUES (?, ?, ?, ?)";
}

function sql_LinkBarcode() {
    return "INSERT INTO barcode_lookup (PART_NUM, barcode)
        VALUES (?, ?)";
}

function sql_Reorders() {
    return "SELECT * FROM parts WHERE status='out_of_stock'";
}

function sql_Empty() {
    return "SELECT * FROM parts WHERE status='no_reorder'";
}

/*  might come of use later...but not now.
function StartSession() {       // function used for making initial connections
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;   // defined in rj-inv_config.php
    
    // Stop JavaScript from being able to access the session id.
    $httponly = true;
 * 
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
} */