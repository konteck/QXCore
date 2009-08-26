<?php

class Error extends QController
{
    private $httpCodes = array
    (
        404 => "Page not found"
    );
    
    function __construct()
    {
        $this->ViewName = "qxc_error";

//        throw new QWebException("", "", "", "", "");
    }

    public function Index($code = 404)
    {
        $this->View->title = "{$this->httpCodes[$code]} | Oops! an error occured";
        $this->View->number = $code;
        $this->View->header = $this->View->Load('qxc_header');
        $this->View->footer = $this->View->Load('qxc_footer');
        $this->View->Render();
    }
}