<?php

class QInput
{
    private $tempVar = "";

    /**
     * @return QModel Returns QModel
     */
    function Post($name = '')
    {
        $var = $this->QXC->getGlobal($name, 'POST');

        return $var;
    }

    /**
     * @param name[optional]
     * @return string|array
     */
    function Get($name = '')
    {
        $this->tempVar = $this->QXC->getGlobal($name, 'GET');
        return $this;
    }

    /**
     * @return QModel Returns QModel
     */
    function Cookie($name)
    {
        $var = $this->QXC->getGlobal($name, 'COOKIE');
        return $var;
    }

    /**
     * @return QModel Returns QModel
     */
    function Session($name)
    {
        $var = $this->QXC->getGlobal($name, 'SESSION');
        return $var;
    }

    /**
     * @return QModel Returns QModel
     */
    function Files()
    {
        $var = $this->QXC->getGlobal($name, 'FILES');
        return $var;
    }

    function __toString()
    {
        return (string)$this->tempVar;
    }
}