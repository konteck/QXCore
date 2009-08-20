<?php

class QConfig
{
    public $Properties;
    public $XPath;    
    private $configDom;
    private $configName = CORE_CONFIG_NAME;

    function __construct($name = '')
    {
        if (!empty($name) && ctype_alnum($name))
        {
            $this->configName = $name;
        }
    }

    // Public Methods
    public function Get($key)
    {
        if (!is_object($this->configDom))
        {
            $this->LoadConfig();
        }

        $val = $this->QXC->Xml->GetByTagName($key);

        return $val[0];
    }

    // Private Methods
    private function LoadConfig()
    {
        $cPath = CONFIG_DIR . '/' . strtolower($this->configName) . "." . CORE_CONFIG_EXTENSION; // TODO remove

        $this->configDom = $this->QXC->Xml->Open($cPath);
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
            // TODO Add  exception throw
        }
            var_dump($name);
        $this->$name = new QConfig($name);
        $this->$name->$name = $this->$name;
        $this->$name->QXC = $this->QXC;

        return $this->$name;
    }
}

?>