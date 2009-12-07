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

    private $mp3dir;

    function __construct()
    {
        // Set default values
        $this->width = 200;
        $this->height = 70;
        $this->dic = CORE_DIR . "/system/captcha/dictionary/words-en.dic";
        $this->format = IMG_PNG;
        $this->background = "resources/captcha-back.png";
        $this->mp3dir = CORE_DIR . "/system/captcha/audio";
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

    public function Play()
    {
        $text = (string)$this->QXC->Request->Session('qxc_captcha_text');
        
        if (empty ($text))
        {
            $text = $this->GenWord();
        }

        $size = 0;

        for ($i = 0; $i < strlen($text); $i++)
        {
            $mp3 .= $this->GetAudio($text[$i], &$size);
        }

        // Send the generated mp3 file
        header("Pragma: no-cache");
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Type: audio/mpeg');
        header("Content-Disposition: attachment; filename=\"validate.mp3\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-length: {$size}");

        die($mp3);
    }

    public function Render()
    {
        $text = $this->GenWord();
        
        $this->InitImage();        
        $this->SetText($text);
//        $this->WaveImage();
        $this->SetBackground();

        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
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

        if (!empty ($text) && strtolower($string) == strtolower($text))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function GetCode()
    {
        $baseUrl = WEB_URL;
        $textSize = $this->width - 40;

        $str = "<img src=\"{$baseUrl}/qxc/captcha/image\" width=\"{$this->width}\" height=\"{$this->height}\" alt=\"code\" style=\"display:block\" />";
        $str .= "<input type=\"text\" name=\"secretcode\" style=\"float: left;width: {$textSize}px;margin: 15px 10px 5px 0\" />";
        $str .= "<object style=\"float: left;margin-top: 10px;\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width=\"35\" height=\"35\" id=\"voice\" align=\"middle\">";
        $str .= " <param name=\"allowScriptAccess\" value=\"sameDomain\" />";
        $str .= " <param name=\"allowFullScreen\" value=\"false\" />";
        $str .= " <param name=\"movie\" value=\"{$baseUrl}/qxc/captcha/audio/voice.swf\" />";
        $str .= " <param name=\"loop\" value=\"false\" />";
        $str .= " <param name=\"menu\" value=\"false\" />";
        $str .= " <param name=\"quality\" value=\"high\" />";
        $str .= " <param name=\"scale\" value=\"noscale\" />";
        $str .= " <param name=\"flashvars\" value=\"webUrl={$baseUrl}\" />";
        $str .= " <param name=\"wmode\" value=\"transparent\" />";
        $str .= " <param name=\"bgcolor\" value=\"#ffffff\" />";
        $str .= "<embed src=\"{$baseUrl}/qxc/captcha/audio/voice.swf\" flashvars=\"webUrl={$baseUrl}\" loop=\"false\" menu=\"false\" quality=\"high\" scale=\"noscale\" wmode=\"transparent\" bgcolor=\"#ffffff\" width=\"35\" height=\"35\" name=\"voice\" align=\"middle\" allowScriptAccess=\"sameDomain\" allowFullScreen=\"false\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.adobe.com/go/getflashplayer\" />";
        $str .= "</object><div style=\"clear:both\"></div>";

        return $str;
    }
    
    private function GenWord()
    {
        // Text
        $lines = file($this->dic, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES | FILE_TEXT);
        $linesNum = count($lines) - 1;

        shuffle($lines);

        $prefix = $lines[rnd($linesNum)];
        $postfix = $lines[rnd($linesNum)];

        $text = rtrim($prefix) . rtrim($postfix);

        $this->QXC->Request->Session('qxc_captcha_text', $text);

        return $text;
    }

    private function InitImage()
    {
        $this->im = imagecreatetruecolor($this->width, $this->height);

        $color = imagecolorallocate($this->im, 255, 255, 255);

        imagefilledrectangle($this->im, 0, 0, $this->width, $this->height, $color);
    }

    private function SetBackground()
    {  
        $image = getimagesize("{$this->PATH}/{$this->background}");
        list($width, $height) = $image;

        switch (array_pop($image))
        {
            case 'image/png':
                $srcImg = imagecreatefrompng("{$this->PATH}/{$this->background}");
                break;
            default:
                break;
        }

        if ($srcImg)
        {
            imagecopy($this->im, $srcImg, $this->width - $width, $this->height - $height, 0, 0, $width, $height);
        }
    }

    private function SetText($text)
    {
        $length = strlen($text);
        $fontSize = round($this->width / $length) - 8;
        $x = ($this->width - ($length * $fontSize)) / 2;
        $y = round($this->height / $fontSize * 13);

        // Foreground color
        $color = $this->colors[array_rand($this->colors)];
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

        for ($i = 0; $i < $length; $i++)
        {
            $degree   = rand(-$this->maxRotation, $this->maxRotation);
            $size = rand($fontSize - 5, $fontSize + 5);

            $coords = imagettftext($this->im, $size, $degree, $x, $y, $color, $fontfile, $text[$i]);

            $x += ($coords[2] - $x) + $this->scale - 5;
        }
    }

    private function WaveImage()
    {
        
        // X-axis wave generation
        $xp = $this->scale * 11 * rand(1,3);
        $k = rand(0, 100);
        
        for ($i = 0; $i < ($this->width * $this->scale); $i++)
        {
            imagecopy($this->im, $this->im, $i-1, sin($k + $i / $xp) * ($this->scale), $i, 0, 1, $this->height * $this->scale);
        }

        // Y-axis wave generation
        $k = rand(0, 100);
        $yp = $this->scale * 11 * rand(1,2);
        for ($i = 0; $i < ($this->height*$this->scale); $i++)
        {
            imagecopy($this->im, $this->im, sin($k+$i/$yp) * ($this->scale), $i-1, 0, $i, $this->width*$this->scale, 1);
        }
    }

    private function GetAudio($letter, &$totalSize)
    {

        $letter = strtolower($letter);

        if(!file_exists("{$this->mp3dir}/{$letter}.mp3"))
            return;

        $handle = fopen("{$this->mp3dir}/{$letter}.mp3", "rb"); // Read as binary
        $size = filesize("{$this->mp3dir}/{$letter}.mp3");

        $totalSize += $size;

        $data = stream_get_line($handle, $size);

        @fclose($handle);

        return $data;
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