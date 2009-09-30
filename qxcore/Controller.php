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
 * @property View           $View
 * @property Model          $Model
 * @property Module         $Module
 */
abstract class Controller extends QXCore
{	
    public $ModelName;
    public $ViewName;
    private $ModuleName;

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
                default :
                    parent::__get($name);
                    break;
            }

            return $this->$name;
        }
    }

    public function View($name = '')
    {
        $this->ViewName = $name;

        if($this->View == null)
        {
            $this->View = $this->loadView();
        }

        $this->View->SetName($name);

        return $this->View;
    }

    public function Model($name = '')
    {
        $this->ModelName = $name;

        if($this->Model == null)
        {
            $this->Model = $this->loadModel();
        }

        $this->Model->SetName($name);

        return $this->Model;
    }

    private function loadView()
    {
        // TODO: Remove this checking - not really need
        $vName = (empty($this->ViewName) ? get_class($this) : $this->ViewName);

        include_once (CORE_DIR . '/qxcore/View.php');

        return new QView($vName);
    }

    private function loadModel()
    {
        $mName = (empty($this->ModelName) ? get_class($this) : $this->ModelName);

        include_once (CORE_DIR . '/qxcore/Model.php');

        $model = new QModel($mName);

        return $model->Load();
    }
}