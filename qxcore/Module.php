<?php

class QModule
{
    private $moduleName;
    private $modulePath;
    
    function __construct()
    {
        
    }

    // Private Methods
    private function loadModule()
    {
        $this->modulePath = "/modules/{$this->moduleName}/index." . CORE_PHP_EXT;
        
        if (file_exists(WEB_DIR . $this->modulePath))
        {
            $this->modulePath = WEB_DIR . $this->modulePath;
        }
        else if(file_exists(CORE_DIR . $this->modulePath))
        {
            $this->modulePath = CORE_DIR . $this->modulePath;
        }
        else
        {
            throw new QWebException("404 Page Not Found",
                "/modules/{$this->moduleName} - doesn't exists!");
        }

        include_once ($this->modulePath);
    }

    // Magic Methods
    protected function __get($name)
    {
        if (isset($name) && ctype_alnum($name))
        {
            $this->moduleName = strtolower($name);
            
            $this->loadModule();

            $qname = "{$name}";

            $this->$name = new $qname(QXC());
            $this->$name->QXC = QXC();
            $this->$name->PATH = dirname($this->modulePath);

            return $this->$name;
        }
        else
        {
            // TODO Add exception handler
            return '';
        }
    }
}