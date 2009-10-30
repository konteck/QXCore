<?php

class QRequest
{   
    private $tempVar = null;
    private $method = null;

    function __construct()
    {
        
    }

    protected function __get($name)
    {
        $methods = array
        (
            'POST',
            'GET',
            'FILES',
            'COOKIE',
            'SESSION'
        );

        $name = strtoupper($name);

        if (isset($name) && in_array($name, $methods))
        {
            $objClone = clone $this;
            $objClone->method = $name;
            
            return $objClone;
        }

        throw new QException("Method {$name} not defined");
    }

    public function __toString()
    {
        return $this->ToString();
    }   

    /**
     * @return Model Returns Model
     */
    public function Post($name = '')
    {
        $objClone = clone $this;
        $objClone->method = "POST";
        $objClone->tempVar = $this->QXC->getGlobal($name, 'POST');

        return $objClone;
    }

    /**
     * @param name[optional]
     * @return string|array
     */
    public function Get($name = '')
    {
        if (empty ($name))
        {
            return $objClone->tempVar = $this->QXC->getGlobal(null, 'GET');
        }
        else
        {
            $objClone = clone $this;
            $objClone->method = "GET";
            $objClone->tempVar = $this->QXC->getGlobal($name, 'GET');

            return $objClone;
        }
    }

    /**
     * @return Model Returns Model
     */
    public function Cookie($name)
    {
        $objClone = clone $this;
        $objClone->method = "COOKIE";
        $objClone->tempVar = $this->QXC->getGlobal($name, 'COOKIE');

        return $objClone;
    }

    /**
     * @return Model Returns Model
     */
    public function Session($name, $value = "")
    {
        if (empty($value))
        {
            $objClone = clone $this;
            $objClone->method = "SESSION";
            $objClone->tempVar = $this->QXC->getGlobal($name, 'SESSION');

            return $objClone;
        }
        else
        {
            $_SESSION[$name] = $value;

            return $this;
        }
    }

    /**
     * @return Model Returns Model
     */
    public function Files($name = "", $var = "")
    {
        $objClone = clone $this;
        $objClone->method = "FILES";
        $objClone->tempVar = $this->QXC->getGlobal($name, 'FILES');

        if (!empty ($name))
        {
            if (!empty ($var))
            {
                return $objClone->tempVar[$var];
            }

            return $objClone->tempVar;
        }

        return $objClone;
    }

    /**
     * Move uploaded file to new location
     * @return bool Returns bool
     */
    public function Move($name, $path)
    {
        if(empty ($this->tempVar))
        {
            $this->tempVar = $this->QXC->getGlobal(null, $this->method);
        }

        move_uploaded_file($this->tempVar[$name]['tmp_name'], $path);

        return true;
    }

    /**
     * TODO Optimize algorithm
     * @param name[optional]
     * @return object|array
     */
    public function Validate($pattern)
    {
        if (empty ($this->method))
        {
            throw new QException("You must use 'Validate' method with an request method");
        }

        $args = func_get_args();

        if (!is_null($this->tempVar) && is_string($pattern))
        {
            if(strpos($pattern, "/") !== 0)
            {
                $pattern = "/{$pattern}/";
            }

            if (preg_match($pattern, $this->tempVar))
            {
                return true;
            }
            else
            {
                if (func_num_args() == 2 && is_string($args[1]))
                {
                    $this->QXC->setGlobal(null, $args[1], 'ERRORS');
                }
                
                return false;
            }
        }       
        else
        {
            foreach ($args as $val)
            {
                $key = (string)$val[0];
                $pattern = (string)$val[1];

                $var = $this->QXC->getGlobal($key, $this->method);

                if($pattern[0] != "/")
                {
                    $pattern = "/^{$pattern}$/";
                }

                if (!preg_match($pattern, $var))
                {
                    $this->QXC->setGlobal($key, $val[2], 'ERRORS');
                }
            }

            return $this;
        }
    }

    public function IsValid()
    {
        $errorsArray = QXC()->getGlobal(null, "ERRORS");
        
        if (is_array($errorsArray) && count($errorsArray) == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function Clean()
    {
        if (empty($this->tempVar) && !empty($this->method))
        {
            $arr = $this->QXC->getGlobal(null, $this->method);
            
            foreach ($arr as $key => $val)
            {
                if (is_string($val))
                {
                    $this->QXC->setGlobal($key, $this->cleanString($val), $this->method);
                }
                else
                {
                    // TODO Write cleaner code for array
                }                
            }
        }
        else
        {
            if (is_string($this->tempVar))
            {
                $this->tempVar = $this->cleanString($this->tempVar);
            }
            else
            {
                // TODO Write cleaner code for array
            }
        }

        return $this;
    }

    public function Equals($string)
    {
        if(!empty ($this->tempVar) && $this->tempVar == $string)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function IsUploaded($name = "")
    {
        $this->tempVar = $this->QXC->getGlobal($name, 'FILES');        

        if(!empty ($this->tempVar))
        {
            $tmpArray = empty($name) ? current($this->tempVar) : $this->tempVar;
            
            if(is_uploaded_file($tmpArray['tmp_name']))
            {
                return true;
            }

            return false;
        }

        return false;
    }

    public function Trim($chars = "")
    {
        if (empty ($chars))
        {
            $this->tempVar = trim($this->tempVar);
        }
        else
        {
            $this->tempVar = trim($this->tempVar, $chars);
        }

        return $this;
    }

    public function ToString()
    {
        return (string)$this->tempVar;
    }

    public function ToArray()
    {
        if (!empty ($this->tempVar))
        {
            return $this->tempVar;
        }
        else if (!empty ($this->method))
        {
            return $this->QXC->getGlobal(null, $this->method);
        }
        else
        {
            throw new QException("Not implemented");
        }
    }

    private function cleanString($input)
    {
        $input = htmlentities($input, ENT_QUOTES, 'UTF-8');

        if(get_magic_quotes_gpc())
        {
            $input = stripslashes($input);
        }
        
        $input = strip_tags($input);

        return trim($input);
    }
}