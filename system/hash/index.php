<?php

class QHash
{
    public function MD5($string, $salt = "")
    {
        $hash = md5($string);

        if (!empty ($salt))
        {
            $hash = md5($hash . $salt);
        }

        return $hash;
    }
}