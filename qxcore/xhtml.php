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

    public function select($attributes = '')
    {
        $value = self::getArray($attributes);
        
        if(array_key_exists('option', $value))
        {
            var_dump($value);

            foreach ($value['option'] as $key => $val)
            {
                $str .= '<option></option>';
            }

            var_dump($key, $val);

//        $value = self::getAttributes($attributes);

            return '<select type="submit" ' . $value . ' />';
        }
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
            
            return '<form ' . $value . '>';
        }
        else
        {
            return '</form>';
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

        
        if(is_array($value))
        {
            array_walk($value, array('X', 'alterItem'));
            
            return rtrim(join(" ", $value));
        }

        return false;
    }

    private function getArray($value)
    {
        if (is_string($value))
        {
            return QXC()->Json->decode("{" . trim($value, "{}") . "}");
        }

        return $value;
    }
}

?>
