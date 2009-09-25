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
            $source = "<pre class='brush: php'>" . $this->getSource() . "</pre>";
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

    private function getSource()
    {
        $range = 5;
        
        $dataArray = file($this->file);

        for ($i = $this->line - $range; $i < $this->line + $range; $i++)
        {
            $str .= $dataArray[$i];
        }

        return $str;
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

        $this->View->title = "{$title} | Oops! an error occured";
        $this->View->message = $message;

        if(DEBUG)
        {
            $this->View->trace = $tracelog;
        }

        $this->View('qxc_error')->Render();

        die();
    }
}