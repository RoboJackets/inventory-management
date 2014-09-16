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
    
    private $part_id;
    private $barcode;
    
    protected $part_num;
    protected $name;
    protected $category;
    protected $description;
    protected $datasheet;
    protected $location;
    
    protected $bags;
    protected $barcodes;
    protected $quantity;
    
    protected $attributes;
    protected $attribute;
    protected $value;
    protected $priority;
    
    protected $num_results;
    
    
    
    // prepares the object when a new one is created
    public function __construct($barcode)
    {
        
        $this->bags = array();
        $this->attributes = array();
        $this->barcode = $barcode;
        
    }   // function __construct
    
    
    
    public function showResults()
    {
        echo "\nPart::showResults:\n";
        foreach($this as $key => $val)
        {
            echo "$key => $val\n";
        }
    }
    
    

    // searches the database for a partnumber when given a barcode
    public function findPartInfo()
    {
        if(empty($this->part_id))
        {
            $this->findPartID();
        }
        if(empty($this->part_num))
        {
            $data_array = $this->filterMany($this->queryDB("SELECT * FROM parts WHERE part_id=(?)", $this->part_id));
            
            
            foreach($data_array as $index => $item)
            {
                foreach($item as $key => $val) { // itterate through all fields
                    $this->$key = $val;
                    echo "$key => $val\n";
                }
                echo "\n";
            }
            
        }
        
    }   // function getPartNum
    
    
    public function findAllBarcodes()
    {
        
        if(empty($this->part_id))
        {
            $this->findPartID();
        }
        
        if(isset($this->part_id))
        {
            $this->barcodes[] = $this->filterMany($this->queryDB("SELECT barcode, quantity, added FROM barcode_lookup WHERE part_id=(?)", $this->part_id));
        }
        
        var_dump($this->barcodes);
        
    }
    
    public function findPartID()
    {
        if(isset($this->barcode))   // this should never be empty since assigned in the constructor
        {
            $temp = $this->filterSingle($this->queryDB("SELECT * FROM barcode_lookup WHERE barcode=(?)", $this->barcode ), 'part_id');
            $this->part_id = array_shift($temp);
        }  
    }
    
    
    private function queryDB($sql, $user_input)
    {
        global $CONN;   // let function know about the global declared connection
        
        $input = (string)$user_input;

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
    }   // function queryDB
    
    
    
    // filters a queries results
    private function filterSingle($query, $field_name)
    {
        $meta = $query->result_metadata();  // get the metadata from the results

        // store the field heading names into an array, pass by reference
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        // callback function; same as: $query->bind_result($params)
        call_user_func_array(array($query, 'bind_result'), $params);
       
        $results = array();
        while ($query->fetch()) {   // fetch the results for every field
            
            $results[] = $row[$field_name];
        }

        // close the open database/query information
        $meta->close();
        $query->close();

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

        $results = array();
        
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
