<?php

class QRequest
{   
    private $tempVar;
    private $method;

    function __construct()
    {
        
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
            return $objClone->tempVar = $this->QXC->getGlobal("", 'GET');
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
        }
        else
        {
            $_SESSION[$name] = $value;
        }
        
        return $objClone;
    }

    /**
     * @return Model Returns Model
     */
    public function Files()
    {
        $objClone = clone $this;
        $objClone->method = "FILES";
        $objClone->tempVar = $this->QXC->getGlobal($name, 'FILES');

        return $objClone;
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

        if (!empty ($this->tempVar) && is_string($pattern))
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

    public function __toString()
    {
        return $this->ToString();
    }

    private function cleanString($string)
    {
        $str = $string;
        $str = urldecode($str);
        $str = strip_tags($str);
        $str = mysql_escape_string($str);

        return trim($str);
    }

    protected function __get($name)
    {
        $methods = array
        (
            'POST',
            'GET',
            'FILE',
            'COOKIE',
            'SESSION'
        );

        if (isset($name) && in_array(strtoupper($name), $methods))
        {
            $objClone = clone $this;
            $objClone->method = "POST";

            return $objClone;
        }

        throw new QException("{$name} not defined");
    }
}