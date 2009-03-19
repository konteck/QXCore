<?php

class url
{
	private $tempVar;
	
    /**
     * @return string|array
     */
	function part($num = '')
	{
		$var = QXC()->getPart($num);
		
		return $var;
	}
	
	/**
	 * @return string|array
	 */
	function get($name = '')
	{
		$this->tempVar = QXC()->getGlobal($name, 'GET');
		return $this;
	}
	
	/**
	 * @return QModel Returns QModel
	 */
	function cookie($name)
	{
		$var = QXC()->getGlobal($name, 'COOKIE');
		return $var;
	}
	
	/**
	 * @return QModel Returns QModel
	 */
	function session($name)
	{
		$var = QXC()->getGlobal($name, 'SESSION');
		return $var;
	}
	
	/**
	 * @return QModel Returns QModel
	 */
	function files()
	{
		$var = QXC()->getGlobal($name, 'FILES');
		return $var;
	}
	
	function __toString()
	{
		return $this->tempVar;
	}
}

?>