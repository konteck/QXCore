<?php

class QView
{
	private $varsArray;
	private $viewName;
	
	function __construct($name = '')
	{
		$this->varsArray = array();
		
		if (! empty($name))
		{
			$this->viewName = strtolower($name) . '_view';
		}
	}
	
	public function Name($name)
	{
		return new $this($name);
	}
	
	public function render()
	{
		$content = $this->loadView();
		
		echo $content;
	}
	
	public function content()
	{
		$content = (string)$this->loadView();
		
		return $content;
	}
	
	public function set($array)
	{
		if (is_array($array) && count($array) > 0)
		{
			$this->varsArray = $array;
		}
		
		return $this;
	}
	
	private function loadView()
	{
		$vPath = APP_DIR . '/views/' . $this->viewName . '.' . CORE_VIEW_EXT;
		
		if (file_exists($vPath))
		{
			// Begin output buffering 
			ob_start();
			
			extract($this->varsArray, EXTR_SKIP);
			
			require_once ($vPath);
			
			$output = ob_get_clean();
		}
		
		return $output;
	}
	
	/** 
	 * Magically set template data. 
	 * 
	 * @param string Key of the data 
	 * @param string Value of the data 
	 */
	function __set($name, $value)
	{
		if (! empty($name))
		{
			$this->varsArray[$name] = $value;
		}
	}
	
	/** 
	 * Magically gets a template variable. 
	 * 
	 * @param string $key 
	 * @return mixed 
	 */
	function __get($name)
	{
		return $this->varsArray[$name];
	}
	
	function __toString() 
	{
		return $this->content();
	}
}

?>