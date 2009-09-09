<?php

class QCaptcha
{
    private $width;
    private $height;
    private $background;
    private $format;
    private $font;
    private $dic;
    
    function __construct()
    {
        // Set default values
        $this->width = 200;
        $this->height = 70;
        $this->dic = CORE_DIR . "/system/captcha/dictionary/words-en.dic";
        $this->format = IMG_PNG;
        $this->background = "captcha-back.png";
    }

    public function SetSize($x, $y)
    {
        if (is_numeric($x) && is_numeric($y))
        {
            $this->width = $x;
            $this->height = $y;
        }
        else
        {
            // TODO write custom exception text
            throw new QException();
        }
    }

    public function Render()
    {
        // Text
        $lines = file($this->dic);
        $text = $lines[rand(0, count($lines) - 1)];

        $this->QXC->Request->Session('qxc_captcha_text', $text);

        // Font
        if(!empty ($this->font))
        {
            $font = $this->font;
        }
        else
        {
            $fontsArray = $this->ReadDir("{$this->PATH}/fonts", "ttf");
            
            $font = "{$this->PATH}/fonts/{$fontsArray[rand(0, count($fontsArray) - 1)]}";
        }

        switch ($this->format)
        {
            case IMG_PNG:                
                $im = imagecreatefrompng("{$this->PATH}/{$this->background}");

                // Color
                $color = imagecolorallocate($im, rand(10, 50), rand(10, 50), rand(10, 50));

                // Add the text
                imagettftext($im, rand(10, 12), rand(-10, 10), 10, rand(13, 15), $color, $font, $text);

                header("Content-type: image/png");
                imagepng($im);
                imagedestroy($im);
                break;
            default:
                break;
        }

        die();
    }

    /**
     * @return true|false
     */
    public function Validate($string)
    {
        if(ctype_alnum($string))
        {
            $text = $this->QXC->Request->Session('qxc_captcha_text');

            if (!empty ($text) && $string == $text)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            throw new QException("Incorrect string format");
        }
    }

    private function ReadDir($dir, $ext = "")
    {
        $files = array();
        
        if($handle = @opendir($dir))
        {
            while (false !== ($file = readdir($handle)))
            {
                if ($file{0} != "." && preg_match("/".$ext."$/i", $file))
                {
                    $files[] = $file;
                }
            }
            closedir($handle);
        }
        else
        {
            throw new QException("Can't open {$dir}");
        }
        
        return $files;
    }
}