<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/6/2014
 * Time: 9:11 PM
 */

class Attribute {

    public $attribute;
    public $value;
    public $priority;

    public function __construct($attribute, $value, $priority)
    {
        $this->attribute = strtolower($attribute);
        $this->value = strtolower($value);
        $this->priority = (string)$priority;
    }   // end of Constructor function


}   // end of Attribute Class