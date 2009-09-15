<?php

class View
{
    private $varsArray = array();
    private $viewName;    

    function __construct($name = '')
    {
        if (! empty($name))
        {
            $this->viewName = strtolower($name) . '_view';
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
            throw new QWebException(404);
        }

        // Begin output buffering
        ob_start();

        extract($this->varsArray, EXTR_SKIP);

        require_once ($vPath);

        $output = ob_get_clean();

        if (preg_match("/{[^}]+}/", $output) && count($this->varsArray) > 0)
        {
            $output = preg_replace(array_map(array($this, 'varsReplace'), array_keys($this->varsArray)), array_values($this->varsArray), $output);
        }

        $errorsArray = QXC()->getGlobal(null, "ERRORS");

        if (is_array($errorsArray) && (bool)count($errorsArray) && stristr($output, "<error>"))
        {
            $output = preg_replace("/<error>/", join("<br />", $errorsArray), $output);
        }

        return $output;
    }

    private function varsReplace($key)
    {
        return "/\{\\$" . $key . "\}/i";
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