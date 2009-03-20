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
			try
			{
				$xml = simplexml_load_file($file);
			}
			catch (Exception $e)
			{
				echo "bad xml";
			}			
			
			return $xml;
		} // TODO add else statment	
	}
}

?>