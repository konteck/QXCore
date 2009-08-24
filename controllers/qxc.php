<?php

class QXC extends QController
{
    function __construct()
    {
//        $this->View->title = "Hello World!";
//        $this->View->header = $this->View->Load('qxc_header');
//        $this->View->footer = $this->View->Load('qxc_footer');
//        $this->View->Render();
    }

    public function handler($resource)
    {
        $filePath = CORE_DIR . "/resources/{$resource}";

        if(file_exists($filePath))
        {
            echo file_get_contents($filePath); // TODO improve read function
        }
    }
}