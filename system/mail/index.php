<?php

class Mail
{
    private $headersArray = array();

    function __construct()
    {
        
    }

    public function SetHeaders($array)
    {
        if (is_array($array) && (bool)$array)
        {
            $this->headersArray = $array;
        }
    }
}