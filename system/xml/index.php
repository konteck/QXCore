<?php

class Xml
{
	/**
	 * @return object
	 */
	public function load($file = '')
	{
		if (file_exists($file))
		{
			$xml = simplexml_load_file($file);
			
			return $xml;
		} // TODO add else statment	
	}
}

?>