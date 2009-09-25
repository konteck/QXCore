<?php

abstract class View
{
    protected $varsArray = array();
    protected $viewName;

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

    function __toString()
    {
        return $this->content();
    }

    public function Load($name)
    {
        return new $this($name, $this->varsArray);
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

    public function SetName($name)
    {
        if (! empty($name))
        {
            $this->viewName = strtolower($name) . '_view';
        }
    }

    public function SetVar($name, $value)
    {
        if (! empty($name))
        {
            $this->varsArray[$name] = $value;
        }
    }

    public function GetVar($name)
    {
        if (! empty($name))
        {
            return $this->varsArray[$name];
        }
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

        $qplex = new QPlexer($vPath, $this->varsArray);

        return $qplex->Render();
    }
}

class QView extends View
{
    /**
     * Magically set template data.
     *
     * @param string Key of the data
     * @param string Value of the data
     */
    function __set($name, $value)
    {
        $this->SetVar($name, $value);
    }

    /**
     * Magically gets a template variable.
     *
     * @param string $key
     * @return mixed
     */
    function __get($name)
    {
        return $this->GetVar($name);
    }
}

// TODO: Improve
class QPlexer extends QPlex
{
    function __get($name)
    {
        if (isset($name) && ctype_alnum($name))
        {
            return QXC()->$name;            
        }
    }
}