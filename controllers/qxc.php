<?php

class QXC extends Controller
{
    private $basePath;
    
    function __construct()
    {

    }

    public function Main()
    {
        $this->View->title = "Hello World!";
        $this->View('qxc')->Render();
    }

    public function Handler($resource)
    {
        if(!empty ($this->basePath))
        {
            $filePath = $this->basePath . "/{$resource}";
        }
        else
        {
            $filePath = CORE_DIR . "/resources/{$resource}";
        }   

        if(file_exists($filePath))
        {
            header("Content-Type: {$this->GetFileType($filePath)}");

            echo file_get_contents($filePath); // TODO improve read function
        }
        else
        {
            die("Resource not found!");
        }
    }

    public function Captcha($type = '', $file = '')
    {
        switch ($type)
        {
            case 'image':
                QXC()->Captcha->Render();
                break;
            case 'audio':
                if($file == "voice.swf")
                {
                    $this->basePath = CORE_DIR . "/system/captcha/resources";
                    $this->Handler("voice.swf");
                }
                else
                {
                    QXC()->Captcha->Play();
                }
                break;
            default:
                echo QXC()->Captcha->GetCode();
                break;
        }
    }

    public function Toolbar($file = '')
    {
        $this->Module->Toolbar->Handler($file);
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