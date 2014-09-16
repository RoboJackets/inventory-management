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
    protected $attributes;

    protected $num_bags;
    protected $total_qty;
    
    
    
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
            
            foreach($data_array as $index => $items)
            {
                foreach($items as $key => $val) { // itterate through all fields
                    $this->$key = $val;
                }
            }
            
        }
        
    }   // function getPartNum
    
    
    
    public function findPartID()
    {
        if(isset($this->barcode))   // this should never be empty since assigned in the constructor
        {
            $temp = $this->filterSingle($this->queryDB("SELECT * FROM barcode_lookup WHERE barcode=(?)", $this->barcode ), 'part_id');
            $this->part_id = array_shift($temp);
        }  
    }
    
    
    
    public function findBarcodes()
    {
        // make sure we can search using the part_id
        if(empty($this->part_id))
        {
            $this->findPartID();
        }
        
        // make sure part_id was found if not before
        if(isset($this->part_id))
        {
            $this->bags = $this->filterMany($this->queryDB("SELECT barcode, quantity, added FROM barcode_lookup WHERE part_id=(?)", $this->part_id));
            $this->num_bags = count($this->bags);
            $this->getQty();    // add up all the quantities for a grand total
        }
    }
    
    
    
    public function findAttributes()
    {
        // make sure we can search using the part_id
        if(empty($this->part_id))
        {
            $this->findPartID();
        }
        
        // make sure part_id was found if not before
        if(isset($this->part_id))
        {
            $this->attributes = $this->filterMany($this->queryDB("SELECT attribute, value, priority FROM attributes WHERE part_id=(?)", $this->part_id));
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
    
    
    
    private function getQty()
    {
        $qty;
        
        foreach ($this->bags as $index->$items)
        {
            foreach ($iteams as $bag->$bag_qty)
            {
                $qty += $bag_qty;
            }
        }
        
        $this->total_qty = $qty;
    }
    
    
    public function showExample()
    {
        
        echo "\n\n\n";
        echo "Example of Data Model:\n==============\n";
        var_dump( json_decode('{"parts":[
    {"part_num":"11593lgy",
    "name":"My Cool Part",
    "category":"ic",
    "description":"A really cool part",
    "datasheet":"www.sketchywebsite.com/datasheet.pdf",
    "location":"A04",
    "bags":[
        {"barcode":"54345432",
        "quantity":"175"
        },
        {"barcode":"1254865",
         "quantity":"20"
        }],
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
     "bags":[
         {"barcode":"943710",
          "quantity":"34"
         },
         {"barcode":"684258",
          "quantity":"1500"
         }],
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
    }
    
    public function testTest()
    {
        echo $this->attributes;
        echo "\n\n";
        echo $this->bags;
    }
    
}
