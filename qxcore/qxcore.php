<?php
/*
 
 QXCore is an extra light php framework released under MIT License
 You should have received a copy of the MIT License along with this program. 
 If not, see http://www.opensource.org/licenses/mit-license.php
 
 LICENSE: MIT License
 
 @copyright   2009 Alex Movsisyan
 @author      Alex Movsisyan - alex@movsisyan.com
 @license     http://www.opensource.org/licenses/mit-license.php
 @link        http://www.qxcore.com/

*/

define('START_TIME', microtime(true));

// Define system varibles
define('CORE_KEY', true);

define('CORE_PHP_EXT', 'php');

define('CORE_VIEW_EXT', 'html');

define('CORE_CNAME', 'Main');

define('CORE_CONFIG', 'app.config');

define('CORE_VER', '0.9');

//$_QXC = NULL;

require_once (CORE_DIR . '/qxcore/ClassDefination.php');
require_once (CORE_DIR . '/qxcore/Core.Config.php');

if (! function_exists('pr'))
{
	/**
	 * For debugging purprouses
	 */
	function pr($object, $terminate = false)
	{
		echo("<pre>");
	
		print_r($object);
		
		if ($terminate) 
		{
			die("<pre>Exec: " . ET());
		}		
	}
}

function ET()
{
	return substr((microtime(true) - START_TIME), 0, 6);
}

/**
 * Get current instance
 * 
 * @return QXCore Returns QXCore instance
 */
function QXC()
{
	if (!is_object(QXCore::$_QXC))
	{ 
		return new QXCore();
	}	

	return QXCore::$_QXC;
}

class QXCore extends ClassDefination
{
	public static $_QXC;
	private $_GLOBALS;
	
	function __construct()
	{
        global $urlRewrite; // TODO remove
        
        parent::unsetVars();    
        		
		QXCore::$_QXC = $this;
				
		$this->_GLOBALS = array();
		
		$this->_GLOBALS['POST'] = $_POST;
		$this->_GLOBALS['GET'] = $_GET;
		$this->_GLOBALS['COOKIE'] = $_COOKIE;
		$this->_GLOBALS['SESSION'] = $_SESSION;
		$this->_GLOBALS['FILES'] = $_FILES;
		
		if ($urlRewrite === true && ! empty($_GET['qstring']))
		{
			$this->_GLOBALS['QSTRING'] = array_map(create_function('$str', 'return (preg_match("/^[a-z0-9\_\-]{1,50}$/", $str))?$str:NULL;'), split("/", $_GET['qstring']));
			
			unset($this->_GLOBALS['GET']['qstring']);
		}
		
		$_GET = $_POST = $_REQUEST = $_COOKIE = $_SESSION = $_FILES = array();
		
		unset($_GET);
		unset($_POST);
		unset($_COOKIE);
		unset($_SESSION);
		unset($_FILES);
		unset($_REQUEST);
		
		$this->loadController();
	}
	
	function __get($name)
	{	
		if (isset($name) && ctype_alnum($name))
		{
			$this->loadModule($name);
			
			return $this->$name = new $name();
		}
		else
		{
			// TODO Add exception handler
			return '';
		}
	}
	
	private function loadModule($name)
	{
		$extPath = CORE_DIR . '/system/' . strtolower($name) . '/index.php';
		
		if (file_exists($extPath))
		{
			include_once ($extPath);
		}
	}
	
	private function loadController()
	{
		global $urlRewrite;
		
		$cName = CORE_CNAME;
		
		if ($urlRewrite && !empty($this->_GLOBALS['QSTRING'])) 
		{
			$cName = $this->getPart(0);
		}
		
		include_once (APP_DIR . '/controllers/' . trim($cName) . '.' . 'php');
		
		new $cName;
	}
	
	public function getGlobal($key, $globalName)
	{
		if (empty($key))
		{
			return (array_key_exists($globalName, $this->_GLOBALS) ? $this->_GLOBALS[$globalName] : NULL);
		}
		else
		{
			return (array_key_exists($key, $this->_GLOBALS[$globalName]) ? $this->_GLOBALS[$globalName][$key] : NULL);
		}
	}	

	public function getPart($num)
	{
		if (empty($num)) 
		{
			return $this->_GLOBALS['QSTRING'];
		}
		else 
		{
			return $this->_GLOBALS['QSTRING'][$num];
		}
		
		
	}
}

require_once (CORE_DIR . '/qxcore/Controller.php');

// Start point
QXC();

?>