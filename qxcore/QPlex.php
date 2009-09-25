<?php

class QPlex
{

    private $tplPath;
    private $varsArray = array();
    
    function __construct($path, $vars = array())
    {
        $this->tplPath = $path;

        if ($vars && is_array($vars))
        {
            $this->varsArray = $vars;
        }
    }

    public function SetVars($array)
    {
        $this->varsArray = $array;
    }

    public function Render()
    {
         // Begin output buffering
        ob_start();

        extract($this->varsArray, EXTR_SKIP);        

        require_once ($this->tplPath);

        $output = ob_get_clean();

        if (preg_match("/{[^}]+}/", $output) && count($this->varsArray) > 0)
        {
            $output = preg_replace(array_map(array($this, 'varsReplace'), array_keys($this->varsArray)), array_values($this->varsArray), $output);

            if (preg_match_all("/{%[\s]?[\"\']+([^\"\']+)[\"\']+}/", $output, $matches))
            {
                foreach ($matches[1] as $val)
                {
                    // TODO: Remove relation with View
                    $output = preg_replace("/{%[\s]?[\"\']+([^\"\']+)[\"\']+}/", new QView($val, $this->varsArray), $output, 1);
                }
            }
        }

        // Display erros, validation etc.
        $errorsArray = QXC()->getGlobal(null, "ERRORS");

        if (is_array($errorsArray) && (bool)count($errorsArray) && stristr($output, "<error>"))
        {
            $output = preg_replace("/<error>/", "<div class='error'>" . join("<br />", $errorsArray) . "</div>", $output);
        }

        return $output;
    }

    private function varsReplace($key)
    {
        return "/\{\\$" . $key . "\}/i";
    }

    // Magic...
    function __toString()
    {
        return $this->Render();
    }
}