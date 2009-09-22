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
        echo "QException throwed";
        echo "<p>";
        echo $this->message = $message;
        echo $this->code = $code;
        $this->severity = $severity;
        $this->file = $filename;
        $this->line = $lineno;

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
}

class QWebException extends Controller
{    
    private $message;
    private $code;
    private $tracelog;
    
    public function __construct($message = "", $tracelog = "")
    {
        $this->message = $message;
        $this->code = $code;
        $this->tracelog = $tracelog;

        $this->ViewName = "qxc_error";

        $this->View->title = "{$message} | Oops! an error occured";
        $this->View->message = $message;

        $this->View->Render();

        die();
    }
}