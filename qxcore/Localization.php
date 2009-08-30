<?php

if (! function_exists('__'))
{
    /**
     * Localization Function TODO Implement
     */
    function __($key)
    {
        $qxc = QXC();

        pr($qxc->Config->Get('language'), 1);
        
    }
}