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

        $titles = $this->configDom->getElementsByTagName($key);

        foreach ($titles as $node)
        {
            echo $node->textContent . "\n";
        }

        pr($titles->item('debug'),1);

        return $this->configDom->$key;
    }

    private function loadConfig()
    {
        $cPath = CONFIG_DIR . '/' . $this->configName; // TODO remove

        $this->configDom = $this->Xml->Open($this->configName);
    }

    function  __call($name, $arguments) {
        echo $name, $arguments;


    }
    function __get($name)
    {
        pr($name."|");
        $cPath = APP_DIR . '/config/' . strtolower($name);

        if (file_exists($cPath))
        {
            $this->$name = new $this($name);
        }
    }
}

?>