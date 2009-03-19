<?php

/**
 * Class Definations
 * 
 * @property QXCore $Contact
 */
abstract class ClassDefination
{
   /** 
    * @var zip 
    */
    public $zip;
    
    function unsetVars() 
    {
        unset($this->zip);
    }
    
	
	/**
	 * @return QModel Returns QModel
	 */
//    public function printOut()
//    {
//    }
}

?>