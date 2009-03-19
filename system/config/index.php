<?php

class Config
{
	private $configName;
	private $configObject;
	
	function __construct($name = '')
	{
		if (! empty($name))
		{
			$this->configName = $name;
		}
		else
		{
			$this->configName = CORE_CONFIG;
		}
	}
	
	public function get($key)
	{
		if (!is_object($this->configObject))
		{
			$this->loadConfig();
		}
		
		pr($this->configObject, 1);
		
		return $key;
	}
	
	private function loadConfig()
	{
		$cPath = CONFIG_DIR . '/' . $this->configName;
		
		$this->configObject = QXC()->Xml->Load($cPath);
	}
	
	function __get($name)
	{
		$cPath = APP_DIR . '/config/' . strtolower($name);
		
		if (file_exists($cPath))
		{
			$this->$name = new $this($name);
		}
	}
}

?>