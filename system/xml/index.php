<?php

class Xml
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
}

?>