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
    
    if($mode!='barcode'){ return; };    // exit and return nothing if mode not supported...yet
    
    $searchResults = new stdClass();    // declare the location where all results will be placed
    
    
    $placeholder = new stdClass();
    $partData = new stdClass();

    $part_id = getPartID($input);

    $placeholder->parts = getPartInfo($part_id);
    
    foreach($placeholder->parts as $key => $val) { // itterate through all fields
            $partData->$key = $val; 
    }
     
    $partData->barcodes = getAllBarcodes($partData->part_id);
    $partData->attributes = getAttributes($partData->part_id);
    
    $SearchResults->parts[] = $partData;
    
    //var_dump($SearchResults);
    
    return json_encode($SearchResults);
}   //  ==========  SearchDB ==========


function getPartID($barcode) {
    $result = FilterResults(queryDB("SELECT part_id FROM barcode_lookup WHERE barcode=(?) LIMIT 1", $barcode));
    return $result[0]->part_id;
}

function getAllBarcodes($part_id) {
    return FilterBarcodes(queryDB("SELECT barcode FROM barcode_lookup WHERE part_id=(?)", $part_id));
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