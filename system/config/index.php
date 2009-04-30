<?php

class QConfig
{
    public $XPath;
    private $configName;
    private $configDom;

    function __construct($name = '')
    {
        if (!empty($name) && ctype_alnum($name))
        {
            $this->configName = $name;
        }
        else
        {
            $this->configName = CORE_CONFIG;
        }
    }

    public function Get($key)
    {
        if (!is_object($this->configDom))
        {
            $this->loadConfig();
        }

        $val = QXC()->Xml->GetByTagName($key);

        return $val[0];
    }

    private function loadConfig()
    {
        $cPath = CONFIG_DIR . '/' . $this->configName; // TODO remove

        $this->configDom = QXC()->Xml->Open($cPath);
    }

    function  __call($name, $arguments) {
        echo $name, $arguments;


    }
    function __get($name)
    {
        $cPath = APP_DIR . '/config/' . strtolower($name);

        if (file_exists($cPath))
        {
            $this->$name = new $this($name);
        }
    }
}

?>