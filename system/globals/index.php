<?php

class globals
{
	private $tempVar;
	
	/**
	 * @return QModel Returns QModel
	 */
	function post($name = '')
	{
		$var = QXC()->getGlobal($name, 'POST');
		
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