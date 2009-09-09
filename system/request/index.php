<?php

class QRequest
{
    private $tempVar;
    private $method;

    /**
     * @return QModel Returns QModel
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
     * @return QModel Returns QModel
     */
    public function Cookie($name)
    {
        $objClone = clone $this;
        $objClone->method = "COOKIE";
        $objClone->tempVar = $this->QXC->getGlobal($name, 'COOKIE');

        return $objClone;
    }

    /**
     * @return QModel Returns QModel
     */
    public function Session($name, $value = "")
    {
        if (is_null($_SESSION))
        {
            session_name("QXC");
            session_start();

            $this->QXC->setGlobal(&$_SESSION, 'SESSION');
        }

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
     * @return QModel Returns QModel
     */
    public function Files()
    {
        $objClone = clone $this;
        $objClone->method = "FILES";
        $objClone->tempVar = $this->QXC->getGlobal($name, 'FILES');

        return $objClone;
    }

    
    public function Validate($pattern)
    {
        if(strpos($pattern, "/") !== 0)
        {
            $pattern = "/{$pattern}/";
        }

        if (preg_match($pattern, $this->tempVar))
        {
            return $this;
        }
        else
        {
            return false;
        }
    }

    public function Clear()
    {
        $str = urldecode($this->tempVar);
        $str = strip_tags($str);
        $str = mysql_escape_string($str);
        
        $this->tempVar = $str;

        return $this;
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

    public function __toString()
    {
        return $this->ToString();
    }
}