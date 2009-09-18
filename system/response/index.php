<?php

class QResponse
{
    public function Redirect($url = "")
    {
        if (empty ($url))
        {
            $url = WEB_URL;
        }
        else if (!preg_match("/^http\:\/\//", $url))
        {
            $url = WEB_URL . "/{$url}";
        }

        if (!headers_sent())
        {
            header("Location: {$url}");
        }

        die($url);
    }

    public function Header($key, $value)
    {
        header($key, $value);
    }
}