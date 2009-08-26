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

    public function ErrorHandler($message = "", $code = "", $severity = "", $filename = "", $lineno = "")
    {
        echo $this->message = $message;
        echo $this->code = $code;
        $this->severity = $severity;
        $this->file = $filename;
        $this->line = $lineno;
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
}

class QWebException extends Exception
{
    protected $severity;
    
    public function __construct($message = "", $code = "", $severity = "", $filename = "", $lineno = "")
    {
        $this->message = $message;
        $this->code = $code;
        $this->severity = $severity;
        $this->file = $filename;
        $this->line = $lineno;
    }

    public function getSeverity()
    {
        return $this->severity;
    }
}