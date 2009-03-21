<?php

class QXml
{
    public $Dom;
    private $domObject;
    private $filePath;

    public function __construct()
    {
        $this->Dom = new XmlDom();
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
                $this->domObject = new DomDocument();
                $this->domObject->load($file);
            }
            catch (Exception $e)
            {
                echo "bad xml";
            }

            return $this->domObject;
        } // TODO add else statment
    }

    public function XPath($string)
    {
        if (is_object($this->domObject) && ctype_alnum($string))
        {
            $xp = new domXPath($this->domObject);

            $q = $xp->query($string);

            foreach ($titles as $node) {
                return $node->textContent;
            }
        }
    }
}

final class XmlDom
{
    public function __construct($object)
    {
        ;
    }
    public function __get($name)
    {
        ;
    }
}

?>