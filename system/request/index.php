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
        $objClone->tempVar = $this->QXC->getGlobal($name, 'COOKIE');

        return $objClone;
    }

    /**
     * @return QModel Returns QModel
     */
    public function Session($name)
    {
        $objClone = clone $this;
        $objClone->tempVar = $this->QXC->getGlobal($name, 'SESSION');

        return $objClone;
    }

    /**
     * @return QModel Returns QModel
     */
    public function Files()
    {
        $objClone = clone $this;
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
        $this->tempVar = strip_tags(mysql_escape_string($this->tempVar));

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

    public function __toString()
    {
        return (string)$this->tempVar;
    }
}