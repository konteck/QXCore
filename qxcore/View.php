<?php

class View
{
    private $varsArray = array();
    private $viewName;    

    function __construct($name = '', $vars = array())
    {
        if (! empty($name))
        {
            $this->viewName = strtolower($name) . '_view';
        }

        if ($vars && is_array($vars))
        {
            $this->varsArray = $vars;
        }
        
        $this->varsArray['web_url'] = WEB_URL;
    }

    public function Load($name)
    {
        return new $this($name);
    }

    public function Render()
    {
        $content = $this->loadView();

        echo $content;
    }

    public function Content()
    {
        $content = (string)$this->loadView();

        return $content;
    }

    public function Set($array)
    {
        if (is_array($array) && count($array) > 0)
        {
            $this->varsArray = $array;
        }

        return $this;
    }

    private function loadView()
    {
        $viewName = "{$this->viewName}." . CORE_VIEW_EXT;

        if (file_exists(WEB_DIR . "/views/{$viewName}"))
        {
            $vPath = WEB_DIR . "/views/{$viewName}";
        }
        else if(file_exists(CORE_DIR . "/views/{$viewName}"))
        {
            $vPath = CORE_DIR . "/views/{$viewName}";
        }
        else
        {
            throw new QWebException("404 Page Not Found",
                "/views/{$viewName} - doesn't exists!");
        }

        $qplex = new QPlex($vPath);        
        $qplex->SetVars($this->varsArray);

        return $qplex->Render();
    }

    /**
     * Magically set template data.
     *
     * @param string Key of the data
     * @param string Value of the data
     */
    function __set($name, $value)
    {
        if (! empty($name))
        {
            $this->varsArray[$name] = $value;
        }
    }

    /**
     * Magically gets a template variable.
     *
     * @param string $key
     * @return mixed
     */
    function __get($name)
    {
        return $this->varsArray[$name];
    }

    function __toString()
    {
        return $this->content();
    }
}