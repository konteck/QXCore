<?php

class QWebException extends Exception
{
    function __construct($number)
    {
        die("Error: " . get_class($this));
    }
}