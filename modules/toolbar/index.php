<?php

class Toolbar
{
    private $data;
    
    function __construct()
    {

    }

    public function Render()
    {
        $this->Initialize();
        
        $str = '<script type="text/javascript" src="{$web_url}/qxc/toolbar/handler/toolbar.js"></script>';
        $str .= "\n";
        $str .= '<link rel="stylesheet" href="{$web_url}/qxc/toolbar/toolbar.css" type="text/css" />';
        $str .= "\n";
        $str .= '<div class="qtoolbar"><div class="left">&nbsp;</div><div class="right">&nbsp;</div><div class="middle">';
        $str .= '<div>';
        $str .= $this->data;
        $str .= '</div>';
        $str .= '</div></div>';
        $str .= "\n";

        return $str;
    }

    public function Handler($resource = '')
    {
        $filePath = $this->PATH . "/resources/{$resource}";

        header("Content-Type: {$this->GetFileType($filePath)}");

        if(file_exists($filePath))
        {
            echo file_get_contents($filePath); // TODO improve read function
        }
        else
        {
            die("File not found!");
        }
    }

    private function Initialize()
    {
        $this->data = "Time: " . T();
        $this->data .= " Memory: " . $this->GetMemoryUsage();
    }

    private function GetMemoryUsage() 
    {
        $mem_usage = memory_get_usage(true);

        if ($mem_usage < 1024)
            return $mem_usage." b";
        elseif ($mem_usage < 1048576)
            return round($mem_usage/1024,2) . " Kb";
        else
            return round($mem_usage/1048576,2) . " Mb";
    }

    private function GetFileType($filename)
    {
        $mimeTypes = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(trim(array_pop(explode('.',$filename))));

        if (array_key_exists($ext, $mimeTypes))
        {
            return $mimeTypes[$ext];
        }
        else
        {
            return 'application/octet-stream';
        }
    }
}