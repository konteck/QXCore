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
            $this->modelName = strtolower($name);
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
        $modelName = "{$this->modelName}_model." . CORE_PHP_EXT;

        if (file_exists(WEB_DIR . "/models/{$modelName}"))
        {
            $modelPath = WEB_DIR . "/models/{$modelName}";
        }
        else if(file_exists(CORE_DIR . "/models/{$modelName}"))
        {
            $modelPath = CORE_DIR . "/models/{$modelName}";
        }
        else
        {
            throw new QException("Model '{$modelName}' not found");
        }

        include_once ($modelPath);

        $mName = "{$this->modelName}Model";

        $this->modelObject = new $mName();
        $this->modelObject->Set($this->varsArray);
        
        return $this;
    }
}

class QModel extends Model
{
    public function __call($name,  $arguments)
    {
        if (is_callable(array($this->modelObject, $name)))
        {
            $result = call_user_func_array(array($this->modelObject, $name), $arguments);
        }
        else
        {
            throw new QException("Model '{$this->modelName}' doesn't contain method '{$name}'");
        }
        
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

