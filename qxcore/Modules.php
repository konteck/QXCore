<?php

class UserModules {
    
    function __construct()
    {
        
    }

    // Private Methods
    private function LoadModule($name)
    {
        $extPath = WEB_DIR . '/modules/' . strtolower($name) . '/index.php';

        if (file_exists($extPath))
        {
            include_once ($extPath);
        }
        else
        {
            throw new QException();
        }
    }

    // Magic Methods
    protected function __get($name)
    {
        if (isset($name) && ctype_alnum($name))
        {
            $this->LoadModule($name);

            $qname = "{$name}";

            $this->$name = new $qname(QXC());
            $this->$name->QXC = QXC();
            $this->$name->PATH = WEB_DIR . '/modules/' . strtolower($name);

            return $this->$name;
        }
        else
        {
            // TODO Add exception handler
            return '';
        }
    }
}