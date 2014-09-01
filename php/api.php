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
function SearchDB($mode, $search_input) {
    global $CONN;   // let function know about the global declared connection
    
    /*
    $search_input = function($search_input) use ($search_input) {
            return htmlspecialchars(stripslashes(trim($search_input)));
        } // cleanup input */
    
    $sql = sql_Barcode();
    
    if(!$query = $CONN->prepare($sql)){
        echo "Error: Could not prepare query statement. (" . $query->errno . ") " . $query->error . "\n";
    }
    if (!$query->bind_param("s", $search_input)) {
        echo "Error: Failed to bind parameters to statement. (" . $query->errno . ") " . $query->error . "\n";
    }
    if (!$query->execute()) {
        echo "Error: Failed to execute query. (" . $query->errno . ") " . $query->error . "\n";
    }
    
    return FilterResults($query);   // return the results after formatting to json data
}   //  ==========  SearchDB ==========

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
        /*foreach($row as $key => $val) { // itterate through all rows
            $temp[$key] = $val; 
        } */
        $result[] = $temp;
    } 
    
    var_dump($result);
    
    $joins = array('attributes' => array('attribute'=>'attribute','value'=>'value','priority'=>'priority'));
    $result = create_join_array($result, $joins);
    
    var_dump($result);
    
    // close the open database/query information
    $meta->close();
    $query->close();
    
    // format the info as json data and return
    //return json_encode($result);
}




function create_join_array($rows, $joins){
    /* build associative multidimensional array with joined tables from query rows */

    foreach((array)$rows as $row){
        if (!isset($out[$row['id']])) {
            $out[$row['id']] = $row;
        }

        foreach($joins as $name => $item){
            unset($newitem);
            foreach($item as $field => $newfield){
                unset($out[$row['id']][$field]);
                if (!empty($row[$field]))
                    $newitem[$newfield] = $row[$field];
            }
            if (!empty($newitem))
                $out[$row['id']][$name][$newitem[key($newitem)]] = $newitem;
        }
    }

    return $out;
}

function sql_Barcode() { // query part information from a barcde
    return "SELECT barcode AS barcodes, "
            . "parts.part_num AS part_num, "
            . "name AS name, "
            . "category AS category, "
            . "description AS description, "
            . "datasheet AS datasheet, "
            . "location AS location, "
            . "attribute AS attribute, "
            . "value AS value, "
            . "priority AS priority "
            //. "GROUP_CONCAT(attributes.attribute) AS AtribKeys, "
            //. "GROUP_CONCAT(attributes.value) AS AtribVals "
            . "FROM barcode_lookup "
                . "LEFT JOIN parts "
                . "ON parts.part_id=barcode_lookup.part_id "
                . "LEFT JOIN attributes "
                . "ON attributes.part_id=parts.part_id "
            . "WHERE barcode_lookup.barcode=(?)";
}   //  ==========  sql_Barcode  ==========

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