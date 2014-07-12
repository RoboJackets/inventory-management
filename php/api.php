<?php
/* 
 * Contains all functions and subroutines used for accessing the db.
 */

// Pass the search mode and input to search for into this subroutine and it does the rest
function SearchDB($mode, $search_input) {
    
    $search_input = function ($search_input) {
            return htmlspecialchars(stripslashes(trim($search_input))); // cleanup the input a bit
        }; // cleanup input
        
        return "break2";
        
    if($query = $CONN->prepare( function($mode) {
            switch ($mode) {
            case "bin":
                return sqlBin();
                break;
            case "barcode":
                return sqlBarcode();
                break;
            default:
                exit(1);    // do not perform db operations without bin or barcode mode specified
            }   // end of switch case
    })) {   // begin when 'if' statement is valid
        
        if (!$query->bind_param('s', $search_input))
            return "Binding Parameters Failed" . $query->errno . ") " . $query->error;

        if (!$query->execute())
            return "Execute Failed: (" . $query->errno . ") " . $query->error;
        
        //if (!$query->bind_result($results))
        //    echo "Binding Results Failed: (" . $query->errno . ") " . $query->error;
    
    }   // end of 'if' statement
    
    return FilterResults($query);   // return the json encoded data after being filtered
}

// This function filters the results for searched data
function FilterResults($result) {
    $response = array();
    if ($result->num_rows) {  // If results are found...
        while($row = $result->fetch_assoc()) {
            $temp['PackageIDs'] = $row['PackageIDs'];
            $temp['PartNum'] = $row['PartNum'];
            $temp['PartName'] = $row['PartName'];
            $temp['PartCat'] = $row['PartCat'];
            $temp['PartDesc'] = $row['PartDesc'];
            $temp['PartSheet'] = $row['PartSheet'];
            $temp['PartLocation'] = $row['PartLocation'];
            $temp['PartErr'] = $row['PartErr'];
            $temp['PartStatus'] = $row['PartStatus'];
            $temp['PartAtrbs'] = $row['PartAtrbs'];
            $temp['PartVals'] = $row['PartVals'];
            $temp['PartPrty'] = $row['PartPrty'];
            // place the data into array of json data
            array_push($response, $temp);
        }
    }
    return json_encode($response);  // return JSON encoded data
}

// sql queries - needs 
function sqlBarcode() {
    return "SELECT
        barcode AS PackageIDs,
        parts.PART_NUM AS PartNum,
        barcode_lookup.added AS BarAdd,
        name AS PartName,
        category AS PartCat,
        description AS PartDesc,
        datasheet AS PartSheet,
        location AS PartLocation,
        flag_error AS PartErr,
        status AS PartStatus,
        parts.updated AS PartUpdated,
        attributes.attribute AS PartAtrbs, 
        attributes.value AS PartVals,
        attributes.priority AS PartPrty
    FROM barcode_lookup
        LEFT JOIN parts 
            ON parts.PART_NUM=barcode_lookup.PART_NUM
            LEFT JOIN attributes 
                ON barcode_lookup.PART_NUM=attributes.PART_NUM
    WHERE barcode_lookup.barcode=(?)";
}

function sqlPart() {
    return "SELECT
        barcode AS PackageIDs,
        parts.PART_NUM AS PartNum,
        barcode_lookup.added AS BarAdd,
        name AS PartName,
        category AS PartCat,
        description AS PartDesc,
        datasheet AS PartSheet,
        location AS PartLocation,
        flag_error AS PartErr,
        status AS PartStatus,
        parts.updated AS PartUpdated,
        attributes.attribute AS PartAtrbs, 
        attributes.value AS PartVals,
        attributes.priority AS PartPrty
    FROM parts
        LEFT JOIN attributes
            ON parts.PART_NUM=attributes.PART_NUM
        LEFT JOIN barcode_lookup
            ON parts.PART_NUM=barcode_lookup.PART_NUM
    WHERE parts.PART_NUM=(?)";
}

function sqlBin() {
    return "SELECT
        barcode AS PackageIDs,
        parts.PART_NUM AS PartNum,
        barcode_lookup.added AS BarAdd,
        name AS PartName,
        category AS PartCat,
        description AS PartDesc,
        datasheet AS PartSheet,
        location AS PartLocation,
        flag_error AS PartErr,
        status AS PartStatus,
        parts.updated AS PartUpdated,
        attributes.attribute AS PartAtrbs, 
        attributes.value AS PartVals,
        attributes.priority AS PartPrty
    FROM parts
        LEFT JOIN attributes
            ON parts.PART_NUM=attributes.PART_NUM
        LEFT JOIN barcode_lookup
            ON parts.PART_NUM=barcode_lookup.PART_NUM
    WHERE parts.location=(?)";
}


/*
function sqlCountAllBar(){
    return "SELECT COUNT(*) FROM barcode_lookup";
}

function sqlCountAllParts(){
    return "SELECT COUNT(*) FROM parts";
}

function sqlGetSimilarBarcodes(){
    return "SELECT COUNT(*) FROM parts WHERE barcode_lookup.PART_NUM=(?)";
}

function AddAttribs() {
    return "INSERT INTO attributes (PART_NUM, attribute, value, priority)
        VALUES ($PartNum, ?, ?, ?)";
}

function LinkBarcode() {
    return "INSERT INTO barcode_lookup (PART_NUM, barcode)
        VALUES ($PartNum, ?)";
}

function sqlReorders() {
    return "SELECT * FROM parts WHERE statue='out_of_stock'";
}

function sqlEmpty() {
    return "SELECT * FROM parts WHERE statue='no_reorder'";
}
*/
 



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