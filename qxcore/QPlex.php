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

        // Display toolbar
        if (DEBUG)
        {
            $output = preg_replace("/<\/body>/", "{$this->Module->Toolbar->Render()}</body>", $output);
        }
        
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

            $output = preg_replace("/{\s?\\$[^}]+}/", "", $output);
        }

        // Display erros, validation etc.
        $errorsArray = QXC()->getGlobal(null, "ERRORS");
        $messagesArray = QXC()->getGlobal(null, "MESSAGES");

        if (is_array($errorsArray) || is_array($messagesArray) && stristr($output, "</body>"))
        {
            $data = "<link rel=\"stylesheet\" href=\"{$web_url}/qxc/handler/jquery.growl.css\" type=\"text/css\" />";
            $data .= "<script type=\"text/javascript\" src=\"{$web_url}/qxc/handler/jquery.growl.js\"></script>";

            $data .= <<<DATA
<script type="text/javascript">
	$(document).ready(function()
	{
DATA;
            if ((bool)count($errorsArray))
            {
                foreach ($errorsArray as $val)
                {
                    $data .= " jQuery.noticeAdd({
                                    text: '" . addslashes($val) . "',
                                    stay: true
                            }); ";
                }
            }

            if ((bool)count($messagesArray))
            {
                foreach ($messagesArray as $val)
                {
                    $data .= " jQuery.noticeAdd({
                                    text: '" . addslashes($val) . "',
                                    stay: true
                            }); ";
                }
            }

$data .= <<<DATA
	});
</script>
DATA;

            $output = preg_replace("/<\/body>/", "{$data}</body>", $output);
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