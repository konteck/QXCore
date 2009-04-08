<?php

/**
* @property QZip           $Zip
*/
class QController extends QXCore
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
			switch (strtolower($name)) {
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
		
		$mPath = APP_DIR . '/models/' . strtolower($mName) . '_model' . '.' . 'php';
		
		if (file_exists($mPath))
		{
			require_once (CORE_DIR . '/qxcore/Model.php');
			include_once ($mPath);
			
			$className = $mName . '_Model';
			
			return new $className();
		}
	}
	
	private function loadView()
	{
		$vName = (empty($ViewName) ? get_class($this) : $ViewName);
		
		require_once (CORE_DIR . '/qxcore/View.php');
		
		return new QView($vName);
	}
	
	private function loadExtension($name)
	{
		$mPath = APP_DIR . '/modeles/' . $name . '_model' . '.' . 'php';
		
		if (file_exists($mPath))
		{
			include_once ($mPath);
			
			$className = $name . '_View';
			
			$this->$name = new $className();
		}
	}
}

?>