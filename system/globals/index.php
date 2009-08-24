<?php

class QGlobals
{
	private $tempVar;
	
	/**
	 * @return QModel Returns QModel
	 */
	function post($name = '')
	{
		$var = $this->QXC->getGlobal($name, 'POST');
		
		return $var;
	}
	
	/**
	 * @return string|array
	 */
	function get($name = '')
	{
		$this->tempVar = $this->QXC->getGlobal($name, 'GET');
		return $this;
	}
	
	/**
	 * @return QModel Returns QModel
	 */
	function cookie($name)
	{
		$var = $this->QXC->getGlobal($name, 'COOKIE');
		return $var;
	}
	
	/**
	 * @return QModel Returns QModel
	 */
	function session($name)
	{
		$var = $this->QXC->getGlobal($name, 'SESSION');
		return $var;
	}
	
	/**
	 * @return QModel Returns QModel
	 */
	function files()
	{
		$var = $this->QXC->getGlobal($name, 'FILES');
		return $var;
	}
	
	function __toString()
	{
		return $this->tempVar;
	}
}

?>