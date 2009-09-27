<?php

class QException extends Exception
{
    protected $severity;
    
    public function getSeverity()
    {
        return $this->severity;
    }

    function __construct()
    {

    }

    public function ErrorHandler($code = "", $message = "", $filename = "", $lineno = "")
    {
        $this->message = $message;
        $this->code = $code;
        $this->severity = $severity;
        $this->file = $filename;
        $this->line = $lineno;

        if (!empty ($this->file) && is_int($this->line))
        {
            $source = $this->codeHighlight();
        }

        new QWebException("Fatal error", $this->message, $source);

        die();
    }

    public function ExceptionHandler($ex)
    {
        switch (get_class($ex))
        {
            case 'QWebException':
                throw new QWebException($ex->getMessage());
                break;
            default:
                break;
        }
    }

    private function codeHighlight()
    {
        $propArray = array
        (
            "brush" => "php",
            "highlight" => 0,
            "first-line" => 1,
            "font-size" => "100%'"
        );

        $rangeSize = 5;

        $dataArray = file($this->file);

        if(count($dataArray) > $rangeSize * 2)
        {
            $propArray['first-line'] = $this->line - $rangeSize - 1;
            $propArray['highlight'] = $this->line;

            $str = "<?php\n";

            for ($i = $this->line - $rangeSize; $i <= $this->line + $rangeSize; $i++)
            {
                $str .= $dataArray[$i - 1];
            }
        }
        else
        {
            $str = $dataArray[$line];
        }      

        // Join Params
        array_walk($propArray, create_function('&$v,$k', '$v=" $k: $v";'));

        $code = sprintf( "<pre class='%s'>%s</pre>", join(";", $propArray), htmlentities($str));

        return $code;
    }
}

class QWebException extends Controller
{    
    private $message;
    private $code;
    private $tracelog;
    
    public function __construct($title = "", $message = "", $tracelog = "")
    {
        $this->message = $title;
        $this->code = $code; // TODO: Remove, unsed
        $this->tracelog = $tracelog;

        $this->View->title = $title;
        $this->View->message = $message;

        if(DEBUG)
        {
            $this->View->trace = $tracelog;
        }

        $this->View('qxc_error')->Render();

        die();
    }
}