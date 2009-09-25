<?php

abstract class Model extends QXCore
{
    protected $modelName;
    public $varsArray = array();
    protected $modelObject;
    
    function __construct($name = '')
    {
        if (! empty($name))
        {
            $this->modelName = strtolower($name) . '_model';
        }
    }

    public function SetName($name)
    {
        $this->modelName = $name;
    }

    public function SetVar($name, $value)
    {
        if (! empty($name))
        {
            $this->varsArray[$name] = $value;
        }
    }

    public function GetVar($name)
    {
        if (! empty($name))
        {
            return $this->varsArray[$name];
        }
    }

    public function ClearVars()
    {
        $this->modelObject->Db->Parameters = array();
    }

    public function Set($array)
    {
        if (is_array($array) && count($array) > 0)
        {
            $this->varsArray = $array;
        }

        return $this;
    }

    public function Load()
    {
        return $this->loadModel();
    }

    private function loadModel()
    {
        $mName = "{$this->modelName}." . CORE_PHP_EXT;

        if (file_exists(WEB_DIR . "/models/{$mName}"))
        {
            $mPath = WEB_DIR . "/models/{$mName}";
        }
        else if(file_exists(CORE_DIR . "/models/{$mName}"))
        {
            $mPath = CORE_DIR . "/models/{$mName}";
        }
        else
        {
            throw new QException("Model {$mName} not found");
        }

        include_once ($mPath);

        $this->modelObject = new $this->modelName();
        $this->modelObject->Set($this->varsArray);
        $this->modelObject->varsArray = &$this->modelObject->Db->Parameters; // TODO: I need this?
        
        return $this;
    }
}

class QModel extends Model
{
    public function __call($name,  $arguments)
    {
        $result = call_user_func_array(array($this->modelObject, $name), $arguments);
        
        return $result;
    }

    /**
     * Magically set template data.
     *
     * @param string Key of the data
     * @param string Value of the data
     */
    function __set($name, $value)
    {
        $this->modelObject->Db->Parameters[":{$name}"] = $value;
    }

    /**
     * Magically gets a template variable.
     *
     * @param string $key
     * @return mixed
     */
    function __get($name)
    {
        return $this->modelObject->Db->Parameters[":{$name}"];
    }
}

// TODO: Remove - not really needed
class QModelVars extends Model
{    
    function __construct($array)
    {
        $this->varsArray = &$array;
    }
}

