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
    private $minSize = 25;
    private $maxSize = 30;

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

        $text = rtrim($prefix) . rtrim($postfix);

        $this->QXC->Request->Session('qxc_captcha_text', $text);

        $this->InitImage();
        $this->SetBackground();
        $this->SetText($text);
        $this->WaveImage();
        
        switch ($this->format)
        {
            case IMG_PNG: 
                header("Content-type: image/png");

                if ($this->blur && function_exists('imagefilter'))
                {
                    imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
                }
                
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
        $string = (string)$string;

        $text = (string)$this->QXC->Request->Session('qxc_captcha_text');

        if (!empty ($text) && $string == $text)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function InitImage()
    {
        $this->im = imagecreatetruecolor($this->width, $this->height);

        $color = imagecolorallocate($this->im, 255, 255, 255);

        imagefilledrectangle($this->im, 0, 0, $this->width, $this->height, $color);
    }

    private function SetBackground()
    {
        $png = imagecreatefrompng("{$this->PATH}/{$this->background}");
        list($width, $height) = getimagesize("{$this->PATH}/{$this->background}");

        imagecopyresampled($this->im, $png, 0, 0, 0, 0, $this->width, $this->height, $width, $height);
    }

    private function SetText($text)
    {
        $length = strlen($text);
        $x = ($this->width - ($length * 20)) / 2;
        $y = round(($this->height / 12) * $this->scale);

        // Foreground color
        $color = $this->colors[rnd(count($this->colors) - 1)];
        $color = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

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

        $lettersMissing = 7 - strlen($text);
        $fontSizefactor = 1 + ($lettersMissing * 0.09);

        for ($i = 0; $i < $length; $i++)
        {
            $degree   = rnd(-$this->maxRotation, $this->maxRotation);
            $fontsize = rand($this->minSize, $this->maxSize) * $fontSizefactor;

            $coords = imagettftext($this->im, $fontsize, $degree, $x, $y, $color, $fontfile, $text[$i]);

            $x += ($coords[2] - $x) + ($this->scale - 5);
        }
    }

    private function WaveImage()
    {
        // X-axis wave generation
        $xp = $this->scale * 11 * rand(1,3);
        $k = rand(0, 100);
        
        for ($i = 0; $i < ($this->width * $this->scale); $i++)
        {
            imagecopy($this->im, $this->im, $i-1, sin($k + $i / $xp) * ($this->scale*$this->Xamplitude), $i, 0, 1, $this->height * $this->scale);
        }

        // Y-axis wave generation
        $k = rand(0, 100);
        $yp = $this->scale * 11 * rand(1,2);
        for ($i = 0; $i < ($this->height*$this->scale); $i++)
        {
            imagecopy($this->im, $this->im, sin($k+$i/$yp) * ($this->scale*$this->Yamplitude), $i-1, 0, $i, $this->width*$this->scale, 1);
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