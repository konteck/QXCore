<?php

class QJson
{
    /**
    * @return Model Returns Model
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