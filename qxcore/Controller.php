<?php

/**
 * @property QZip           $Zip
 * @property QDb            $Db
 * @property QLog           $Log
 * @property QXml           $Xml
 * @property QJson          $Json
 * @property QCaptcha       $Captcha
 * @property QRequest       $Request
 * @property QResponse      $Response
 * @property View          $View
 * @property QMode          $Model
 */
class Controller extends QXCore
{	
    public $ModelName = '';  //TODO: delete this
    public $ViewName = '';

    function __construct()
    {

    }

    function __get($name)
    {
        if (isset($name) && ctype_alnum($name))
        {
            switch (strtolower($name))
            {
                case 'model' :
                    $this->$name = $this->loadModel();
                    break;
                case 'view' :
                    $this->$name = $this->loadView();
                    break;
                case 'user' :
                    $this->$name = $this->loadExtension($name);
                    break;
                default :
                    parent::__get($name);
                    break;
            }

            return $this->$name;
        }
    }

    private function loadModel()
    {
        $mName = (empty($ModelName) ? get_class($this) : $ModelName);
        $mName = strtolower($mName);

        $mPath = WEB_DIR . "/models/{$mName}_model." . CORE_PHP_EXT;

        if (file_exists($mPath))
        {
            include_once (CORE_DIR . '/qxcore/Model.php');
            include_once ($mPath);

            $className = $mName . '_Model';

            return new $className();
        }
    }

    private function loadView()
    {
        $vName = (empty($this->ViewName) ? get_class($this) : $this->ViewName);

        include_once (CORE_DIR . '/qxcore/View.php');

        return new View($vName);
    }

    private function loadExtension($name)
    {
        $mPath = WEB_DIR . "/modeles/{$name}_model." . CORE_PHP_EXT;

        if (file_exists($mPath))
        {
            include_once ($mPath);

            $className = $name . '_View';

            $this->$name = new $className();
        }
    }
}