<?php

class QWebException extends Exception
{
    function __construct($number)
    {
        $number = (int)$number;
        
        include_once (CORE_DIR . '/qxcore/View.php');

        $view = new QView('base_layout');
        $view->title = "{$number} | Oops! an error occured";
        $view->content = new QView('error_page');
        $view->number = $number;
        $view->Render();

        die();
    }
}