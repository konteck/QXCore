<?php

class X {
    
    public function textbox($attributes = '')
    {
        $value = self::getAttributes($attributes);

        return '<input type="text" ' . $value . ' />';
    }

    public function submit($attributes = '')
    {
        $value = self::getAttributes($attributes);
        
        return '<input type="submit" ' . $value . ' />';
    }

    public function button($attributes = '')
    {
        $value = self::getAttributes($attributes);

        return '<input type="button" ' . $value . ' />';
    }

    public function radio($attributes = '')
    {
        $value = self::getAttributes($attributes);

        return '<input type="radio" ' . $value . ' />';
    }

    public function form($attributes = '')
    {
        if((bool)func_num_args())
        {
            $value = self::getAttributes($attributes);
            
            echo '<form ' . $value . '>';
        }
        else
        {
            echo '</form>';
        }
    }

    private function alterItem(&$val, $key)
    {
        $key = trim($key);
        
        if(!ctype_alnum($key))
        {
            $val = false;
        }
        else
        {
            $val = sprintf('%s="%s"', $key, trim($val, "\"\'"));
        }
    }

    private function getAttributes($value)
    {   
        if (is_string($value))
        {
            if(!($value = json_decode("{" . trim($value, "{}") . "}", true)))
                return false;
        }
        
        if(is_array($value))
        {
            array_walk($value, array('X', 'alterItem'));
            
            return rtrim(join(" ", $value));
        }

        return false;
    }
}

?>
