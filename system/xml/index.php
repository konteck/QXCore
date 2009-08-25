<?php

class QXml extends DOMDocument
{
//    public $Dom;
    private $domObject;
    private $filePath;

    public function __construct()
    {
        parent::__construct();        
    }

    /**
     * @return object
     */
    public function Open($file = '')
    {
        $this->filePath = $file;

        if (file_exists($file))
        {
            try
            {
                $this->domObject = new $this();
                $this->domObject->load($file);
            }
            catch (Exception $e)
            {
                echo "bad xml";
            }

            return $this;
        }
        // TODO add else statment
    }

    public function GetByTagName($name)
    {
        $elements = $this->domObject->getElementsByTagName($name);

        if (!is_null($elements) && count($elements) > 0)
        {
            $arr = array();

            foreach ($elements as $node)
            {
                $arr[] = $node->textContent;
            }

            return $arr;
        }
        // TODO add else
    }

    public function XPath($string)
    {
        if (is_object($this->domObject) && ctype_print($string))
        {
            $xp = new domXPath($this->domObject);

            $q = $xp->query($string);

            $tmpArray = array();

            foreach ($q as $node) 
            {
                $tmpArray[] = $node->textContent;
            }

            return $tmpArray;;
        }
    }
}
