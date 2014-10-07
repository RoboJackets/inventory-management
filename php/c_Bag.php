<?php

/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/6/2014
 * Time: 9:10 PM
 */
class Bag
{

    public $barcode;
    public $quantity;

    public function __construct($barcode, $quantity)
    {
        $this->barcode = $barcode;
        $this->quantity = $quantity;
    }   // end of Constructor function

}   // end of Bag Class