<?php

class QConfig
{
    public $Properties;
    private $xmlObject;
    private $configName = CORE_CONFIG_NAME;

    function __construct($name = '')
    {
        if (!empty($name) && ctype_alnum($name))
        {
            $this->configName = $name;
        }

        if (!is_object($this->xmlObject))
        {
            $this->loadConfig();
        }
    }

    // Public Methods
    public function Get($key)
    {
        $val = $this->xmlObject->GetByTagName($key);

        switch (trim(strtolower($val[0])))
        {
            case "true":
                return true;
                break;
            case "false":
                return false;
                break;
            default:
                return $val[0];
                break;
        }
    }

    public function XPath($expression)
    {
        $array = $this->xmlObject->XPath($expression);

        return $array[0];
    }

    // Private Methods
    private function loadConfig()
    {
        $cPath = CONFIG_DIR . '/' . strtolower($this->configName) . "." . CORE_CONFIG_EXTENSION;

        $this->xmlObject = QXC()->Xml->Open($cPath);
    }

    // Magic Methods
    function  __call($name, $arguments)
    {
        echo $name, $arguments;
    }

    function __get($name)
    {
        if (!ctype_alnum($name))
        {
            // TODO Add exception throw
        }
        
        $this->$name = new $this($name);
        $this->$name->$name = $this->$name;
        $this->$name->QXC = $this->QXC;

        return $this->$name;
    }
}

?>