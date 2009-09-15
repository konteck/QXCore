<?php

class QUrl
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
	 * @return Model Returns Model
	 */
	function cookie($name)
	{
		$var = QXC()->getGlobal($name, 'COOKIE');
		return $var;
	}
	
	/**
	 * @return Model Returns Model
	 */
	function session($name)
	{
		$var = QXC()->getGlobal($name, 'SESSION');
		return $var;
	}
	
	/**
	 * @return Model Returns Model
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