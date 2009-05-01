<?php

class QJson
{
    /**
    * @return QModel Returns QModel
    */
    function encode($string)
    {
        return json_encode($string, true);
    }

    function decode($string)
    {
        return json_decode($string, true);
    }
}

?>