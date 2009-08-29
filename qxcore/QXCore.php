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

define('START_TIME'           , microtime(true));

// Define system varibles
define('CORE_KEY'             , true);
define('CORE_PHP_EXT'         , 'php');
define('CORE_VIEW_EXT'        , 'html');
define('CORE_CONFIG_NAME'     , 'web');
define('CORE_CONFIG_EXTENSION', 'config.xml');
define('CORE_MAIN_CONTROLLER' , 'Main');
define('CORE_VER'             , '0.92');

//--

require_once (CORE_DIR . '/qxcore/Controller.php');
require_once (CORE_DIR . '/qxcore/xhtml.php');

if (! function_exists('pr'))
{
    /**
     * For debugging purprouses
     */
    function pr($object, $terminate = false)
    {
        echo("<pre>");

        var_dump($object);

        if ($terminate)
        {
            die("Exec: " . ET());
        }

        echo("</pre>");
    }
}

if (! function_exists('_'))
{
    /**
     * Localization Function TODO Implement
     */
    function _($key)
    {
        echo $key;
    }
}

function ET()
{
    return substr((microtime(true) - START_TIME), 0, 6);
}

/**
 * Get current instance
 * Alias to QXCore::GetInstance() method
 *
 * @return QXCore Returns QXCore instance
 */
function QXC()
{
    return QXCore::GetInstance();
}

class QXCore
{
    private static $QXC;
    private $GLOBALS;
    private $queryStringArray = array();

    public $test = 'test';

    public function test()
    {
        return $this->test;
    }

    function __construct()
    {
        $this->GLOBALS = array();

        $this->GLOBALS['POST'] = $_POST;
        $this->GLOBALS['GET'] = $_GET;
        $this->GLOBALS['COOKIE'] = $_COOKIE;
        $this->GLOBALS['SESSION'] = $_SESSION;
        $this->GLOBALS['FILES'] = $_FILES;

        if (!empty($_GET['qstring']))
        {
            $this->queryStringArray = array_map(create_function('$str', 'return (preg_match("/^[\w\-\.]{1,50}$/", trim($str)))?$str:NULL;'), split("/", $_GET['qstring']));
        }

        $_GET = $_POST = $_REQUEST = $_COOKIE = $_SESSION = $_FILES = array();        
    }

    public function Initialize()
    {
        // Initialize Logger
        $this->Log->Trace('Start Loading: ' . START_TIME);
                
        // Is in Debug mode
        define("DEBUG", (bool)$this->Config->Get('debug'));

        // Initialize Custom Error Handler
        set_error_handler(array($this->Exception, ErrorHandler), E_ALL & ~E_NOTICE);

        // Initialize Custom Exception Handler
//        set_exception_handler(array($this->Exception, ExceptionHandler));
     
        // Load necessary controllers
        $this->loadController();
    }

    private function loadController()
    {
        if ((bool)count($this->queryStringArray))
        {
            $cName = $this->getPart(0);
        }
        
        $cName = (is_null($cName) || empty ($cName)) ? CORE_MAIN_CONTROLLER : strtolower(trim($cName));

        $cFileName = "{$cName}." . CORE_PHP_EXT;

        if (file_exists(WEB_DIR . "/controllers/{$cFileName}"))
        {
            $cPath = WEB_DIR . "/controllers/{$cFileName}";
        }
        else if(file_exists(CORE_DIR . "/controllers/{$cFileName}"))
        {
            $cPath = CORE_DIR . "/controllers/{$cFileName}";
        }
        else
        {
            throw new QWebException("Page not found", 404);
        }

        include_once ($cPath);

        $controller = new $cName();

        if(count($this->queryStringArray) > 1)
        {
            $methodName = $this->getPart(1);            
        }

        if(!empty($methodName) && is_callable(array($controller, $methodName)))
        {
            array_shift($this->queryStringArray);
            array_shift($this->queryStringArray);

            call_user_func_array(array($controller, $methodName), $this->queryStringArray);
        }
        else if(is_callable(array($controller, "Index")))
        {
            array_shift($this->queryStringArray);
            
            call_user_func_array(array($controller, "Index"), $this->queryStringArray);
        }
        else
        {
            // TODO write else statement
        }
    }

    public function getGlobal($key, $globalName)
    {
        if (empty($key))
        {
            return (array_key_exists($globalName, $this->GLOBALS) ? $this->GLOBALS[$globalName] : NULL);
        }
        else
        {
            return (array_key_exists($key, $this->GLOBALS[$globalName]) ? $this->GLOBALS[$globalName][$key] : NULL);
        }
    }

    public function getPart($num)
    {
        if (is_numeric($num))
        {
            return $this->queryStringArray[$num];
        }
    }

    public static function GetInstance()
    {
        if (!self::$QXC instanceof QXCore)
        {
            self::$QXC = new self();
        }
        
        return self::$QXC;
    }

    // Private Methods
    private function LoadModule($name)
    {
        $extPath = CORE_DIR . '/system/' . strtolower($name) . '/index.php';

        if (file_exists($extPath))
        {
            include_once ($extPath);
        }
        else
        {
            // TODO Write else statement
        }
    }

    // Magic Methods
    protected function __get($name)
    {
        if (isset($name) && ctype_alnum($name))
        {
            $this->LoadModule($name);

            $qname = "Q" . $name;

            $this->$name = new $qname();
            $this->$name->QXC = self::$QXC;

            if(is_callable(array($this->$name, "Initialize")))
            {
                $this->$name->Initialize();
            }

            return $this->$name;
        }
        else
        {
            // TODO Add exception handler
            return '';
        }
    }
}

// Start point
QXC()->Initialize();
