<?php

class QCaptcha
{
    public $width;
    public $height;
    public $background;
    public $format;
    public $font;
    public $dic;
    public $blur;
    public $scale = 8;

    private $maxRotation = 8;
    private $minSize = 10;
    private $maxSize = 20;

    private $im;
    private $colors = array // Foreground colors in RGB-array
    (
        array(27,78,181), // blue
        array(22,163,35), // green
        array(214,36,7),  // red
    );
    
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
        $lines = file($this->dic, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES | FILE_TEXT);
        $linesNum = count($lines) - 1;

        shuffle($lines);

        $prefix = $lines[rnd($linesNum)];
        $postfix = $lines[rnd($linesNum)];

        $text = $prefix . $postfix;

        $this->QXC->Request->Session('qxc_captcha_text', $text);

        // Font
        if(!empty ($this->font))
        {
            $fontfile = $this->font;
        }
        else
        {
            $fontsArray = $this->ReadDir("{$this->PATH}/fonts", "ttf");
            
            $fontfile = "{$this->PATH}/fonts/{$fontsArray[rand(0, count($fontsArray) - 1)]}";
        }

        $this->InitImage();
        $this->SetBackground();
//        $this->SetText();

        switch ($this->format)
        {
            case IMG_PNG: 
                header("Content-type: image/png");
                imagepng($this->im);
                imagedestroy($this->im);
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

    private function InitImage()
    {
        $this->im = imagecreatetruecolor($this->width, $this->height);

        $color = imagecolorallocate($this->im, 205, 255, 255);

        imagefilledrectangle($this->im, 0, 0, $this->width, $this->height, $color);
    }

    private function SetBackground()
    {
        $png = imagecreatefrompng("{$this->PATH}/{$this->background}");

        imagecopyresampled($this->im, $png, 10, 30, 0, 0, $this->width, $this->height, $this->width, $this->height);
    }

    private function SetText()
    {
        $length = strlen($text);
        $x = 5 * $this->scale;
        $y = round(($this->height /40) * $this->scale);

        // Foreground color
        $color = $this->colors[rnd(count($this->colors) - 1)];
        $color = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

        for ($i = 0; $i < $length; $i++)
        {
            $degree   = rnd($this->maxRotation * -1, $this->maxRotation);

            $fontsize = rnd($this->minSize, $this->maxSize);

            $coords = imagettftext($this->im, $fontsize, $degree, $x, $y, $color, $fontfile, $text[$i]);
            //                     var_dump($fontsize);
            //                     die;

            //                    $coords = imagettftext($this->im, $fontsize, $degree, $x, $y, $color, $fontfile, $text[$i]);

            $x += ($coords[2] - $x) + ($this->scale * -1);
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