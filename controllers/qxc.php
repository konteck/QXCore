<?php

class QXC extends QController
{
    function __construct()
    {

    }

    public function Main()
    {
        $this->View->title = "Hello World!";
        $this->View->header = $this->View->Load('qxc_header');
        $this->View->footer = $this->View->Load('qxc_footer');
        $this->View->Render();
    }

    public function Handler($resource)
    {
        $filePath = CORE_DIR . "/resources/{$resource}";

        header("Content-Type: {$this->GetFileType($filePath)}");

        if(file_exists($filePath))
        {
            echo file_get_contents($filePath); // TODO improve read function
        }
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