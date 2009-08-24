<?php

class Error extends QController
{
    public function Index($error = 404)
    {
        $number = (int)$error;
        
        $this->ViewName = "qxc_error";
        $this->View->title = "{$number} | Oops! an error occured";
        $this->View->number = $number;
        $this->View->header = $this->View->Load('qxc_header');
        $this->View->footer = $this->View->Load('qxc_footer');
        $this->View->Render();
    }
}