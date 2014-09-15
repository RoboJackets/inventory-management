<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of c_PartSearch
 *
 * @author Jonathan
 */
class Part {
    
    protected $part_id;
    protected $part_num;
    protected $name;
    protected $category;
    protected $description;
    protected $datasheet;
    protected $location;
    
    protected $bags;
    protected $barcodes;
    protected $quantity;
    
    protected $barcode;
    
    protected $attributes;
    protected $attribute;
    protected $value;
    protected $priority;
    
    protected $num_results;
    
    function showResults()
    {
        echo "Part::showResults:\n";
        foreach($this as $key => $val)
        {
            echo "$key => $val";
        }
    }
    
    // prepares the object when a new one is created
    function __construct($barcode)
    {
        $this->bags = array();
        $this->attributes = array();
        $this->barcode = $barcode;
        
    }   // function __construct
    
    
    
    // searches the database for a partnumber when given a barcode
    function findPartInfo()
    {
        if(empty($this->part_id))
        {
            findPartID();
        }
        if(empty($this->part_num))
        {
            $data_array = filterMany(queryDB("SELECT * FROM parts WHERE part_id=(?)", $this->part_id));
                    
            foreach($data_array as $key => $val) { // itterate through all fields
                $this->$key = $val; 
            }
            
        }
        
    }   // function getPartNum
    
    
    function findAllBarcodes()
    {
        
        if(empty($this->part_id))
        {
            findPartID();
        }
        
        if(isset($this->part_id))
        {
            $this->barcodes[] = filterMany(queryDB("SELECT barcode, quantity, added FROM barcode_lookup WHERE part_id=(?)", $this->part_id));
        }
        
    }
    
    function findPartID()
    {
        echo "Made it!\n\n";
        if(isset($this->barcode))   // this should never be empty since assigned in the constructor
        {
            echo "Made it into barcode being set from findPartID()!\n\n";
            $this->part_id = filterSingle(queryDB("SELECT part_id FROM barcode_lookup WHERE barcode=(?)", $this->barcode), "part_id");
        }  
    }
    
    
    function queryDB($sql, $input)
    {
        
        echo "Made it into queryDB!\n\n";
        
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
        
        echo "Made it past db setup!\n\n";
        
        return $query;   // return the results after formatting to an arry of php objects
    }   // function queryDB
    
    
    
    // filters a queries results
    function filterSingle($query, $field_name)
    {
        
        echo "Made it into filterSingle!\n\n";
        
        $meta = $query->result_metadata();  // get the metadata from the results

        // store the field heading names into an array, pass by reference
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        // callback function; same as: $query->bind_result($params)
        call_user_func_array(array($query, 'bind_result'), $params);

        while ($query->fetch()) {   // fetch the results for every field
            // add row (now as object) to the array of results
            $results[] = $row[$field_name];
        }

        // close the open database/query information
        $meta->close();
        $query->close();

        echo "Made it past filterSingle!\n\n";
        
        // format the info as json data and return
        return $results;
    }   // function filterSingle
    
    
    
    private function filterMany($query)
    {
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

        return $results;
    }   // function filterMany
    
}
