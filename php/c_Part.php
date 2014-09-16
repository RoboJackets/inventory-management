<?php

/**
 * Description of the Part Class
 *
 * @author Jonathan Jones
 */

class Part {
    
    public $part_id;
    public $barcode;
    
    public $part_num;
    public $name;
    public $category;
    public $description;
    public $datasheet;
    public $location;
    
    public $bags;
    public $attributes;

    public $num_bags;
    public $total_qty;
    
    // prepares the object when a new one is created
    public function __construct($barcode)
    {
        $this->bags = array();
        $this->attributes = array();
        $this->barcode = $barcode;
        
    }   // function __construct
    
    
    
    public function get_part_id() {
        return $this->part_id;
    }

    public function get_barcode() {
        return $this->barcode;
    }
    
    public function showResults()
    {
        echo "\nPart::showResults:\n";
        foreach($this as $key => $val)
        {
            echo "$key => $val\n";
        }
    }
    
    public function sendPart()
    {
        
        $temp = array();
        
        $temp['parts'] = $this;
        
        echo json_encode($temp);
    }

    // searches the database for a partnumber when given a barcode
    public function findPartInfo()
    {
        if(empty($this->part_id))
        {
            $this->findPartID();
        }
        if(empty($this->part_num) | empty($this->location))
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
        
        // if no result was found, assume the user input was a part number
        if (empty($this->part_id))
        {
            // move user's input to part number field and remove from barcode field
            $this->part_num = $this->barcode;
            $this->barcode = "None";
            
            // search again for the part's id number
            $temp = $this->filterSingle($this->queryDB("SELECT part_id FROM parts WHERE part_num=(?) LIMIT 1", $this->part_num), 'part_id');
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
        $qty = array();
        
        foreach ($this->bags as $index=>$items)
        {
            foreach ($items as $bag=>$bag_qty)
            {
                $qty[$bag] += $bag_qty;
            }
        }
        
        $this->total_qty = $qty['quantity'];
    }
    
    
    
    // This function will perform the most common purpose of this Class - finding all
    // informaion on a single given part number/barcode.
    public function locateAllInfo()
    {
        $this->findPartID();
        $this->findBarcodes();
        $this->findAttributes();
        $this->findPartInfo();
    }
    
    
    public function outputResultBox()
    {
        echo '<div id="results-pane" class="container">';
        echo '<div class="row">';
        echo '<div class="col-xs-12 space"></div>';
        echo '</div>';
        echo '<div class="row">';
        echo '<div class="col-xs-12">';
        echo '<div class="panel panel-primary">';
        echo '<div class="panel-heading">';
        echo '<div id="part-location-data" class="part-location">';
        
        echo $this->location;
        
        echo '</div>';
        echo '<div class="part">';
        echo '<div id="part-name-data" class="part-name">';
        
        echo $this->name;
        
        echo '</div>';
        echo '<div id="part-num-data" class="part-num">';
        
        echo 'PN: ' . $this->part_num . '  | Bags: ' . count($this->bags) . '  | Qty: ' . $this->total_qty;
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '<div class="panel-body">';
        echo '<dl class="dl-horizontal">';
        
        $this->outputAttributeBox();
        
        echo '</dl>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
    public function outputAttributeBox()
    {
        foreach($this->attributes as $index => $data)
        {
            foreach($data as $attrib => $val)
            {
                if ($attrib == 'attribute')
                {
                    echo '<dt>' . $val . '</dt>';
                }
                if ($attrib == 'value')
                {
                    echo '<dd>' . $val . '</dd>';   
                }
            }
        }
        
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
    
}   // end of Part class
