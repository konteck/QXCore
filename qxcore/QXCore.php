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

define('START_TIME'             , microtime(true));

// Define system varibles
define('CORE_KEY'               , true);
define('CORE_PHP_EXT'           , 'php');
define('CORE_VIEW_EXT'          , 'html');
define('CORE_CONFIG_NAME'       , 'web');
define('CORE_CONFIG_EXTENSION'  , 'config.xml');
define('CORE_MAIN_CONTROLLER'   , 'Main');
define('CORE_VER'               , '0.92');

// Regex predefined patterns
define('__EMAIL__'              , '[\w\-\.]{1,20}@[\w\-\.]{2,20}\.[a-zA-Z]{2,4}');
define('__URL__'                , '(?:https?|ftp)://[\w\-\.]+\.[a-zA-Z]{2,4}/?');
define('__PASSWORD__'           , '[\w]{4,12}');
define('__EMPTY__'              , '[\s]*');
define('__NOTEMPTY__'           , '[^\s]+');

//--

require_once (CORE_DIR . '/qxcore/Controller.php');
//require_once (CORE_DIR . '/qxcore/Localization.php'); // TODO: Implement

if (!class_exists("QPlex"))
{
    require_once (CORE_DIR . '/qxcore/QPlex.php');
}

if (! function_exists('pr'))
{
    /**
     * For debugging purprouses
     */
    function pr($object, $terminate = true)
    {
        echo("<pre>");

        var_dump($object);

        if ($terminate)
        {
            die("Exec: " . T());
        }

        echo("</pre>");
    }
}

function rnd($min = 0, $max = 0)
{
    srand((double)microtime() * 1234567);

    if ($max > 0)
    {
        return rand($min, $max);
    }
    else if($min > 0)
    {
        return rand(0, $min);
    }
    else
    {
        return rand();
    }
}

function T()
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

    function __construct()
    {
        if (!headers_sent())
        {
            header("Powered-By: QXCore");
        }

        $this->GLOBALS = array();

        $this->GLOBALS['POST'] = $_POST;
        $this->GLOBALS['GET'] = $_GET;
        $this->GLOBALS['COOKIE'] = $_COOKIE;
        $this->GLOBALS['FILES'] = $_FILES;
        $this->GLOBALS['ERRORS'] = array();

        if (is_null($_SESSION))
        {
            session_name("QXC");
            session_start();

            $this->GLOBALS['SESSION'] = &$_SESSION;
        }

        $this->queryStringArray = $this->ParseURI();

        $_GET = $_POST = $_REQUEST = $_COOKIE = $_FILES = array();
    }

    public function Initialize()
    {
        // Initialize Logger
        $this->Log->Trace('Start Loading: ' . START_TIME);
                
        // Is in Debug mode
        define("DEBUG", (bool)$this->Config->Get('debug'));

        // Compress output
        define("GZIP_IT", strstr($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip') && (bool)$this->Config->Get('compress_output'));

        // Initialize Custom Error Handler
        set_error_handler(array($this->Exception, ErrorHandler), E_ALL & ~E_NOTICE);

        // Initialize Custom Exception Handler
//        set_exception_handler(array($this->Exception, ExceptionHandler));

        if (GZIP_IT)
        {
            ob_start();
            ob_implicit_flush(0);

            // Load necessary controllers
            $this->loadController();

            $contents = ob_get_clean();

            $gzip_contents = gzencode($contents, 5);

            header('Content-Encoding: gzip');
            header('Content-Length: ' . strlen($gzip_contents));
            die($gzip_contents);
        }
        else
        {
            // Load necessary controllers
            $this->loadController();
        }
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
            throw new QWebException("404 Page Not Found",
                "<strong>/controllers/{$cFileName}</strong> - controller doesn't exists!");
        }

        include_once ($cPath);

        $controller = new $cName();

        // Detect called controller method name
        $methodName = $this->getPart(1);            

        if (empty($methodName))
        {
            $methodName = "Main";

            array_shift($this->queryStringArray);
        }
        else
        {
            array_shift($this->queryStringArray);
            array_shift($this->queryStringArray);
        }

        if(is_callable(array($controller, $methodName)))
        {
            if(empty ($controller->ViewName))
            {
                // Automatically set View name
                $controller->ViewName = ($cName == CORE_MAIN_CONTROLLER) ? strtolower("main") : strtolower("{$cName}/{$methodName}");
            }
            
            call_user_func_array(array($controller, $methodName), $this->queryStringArray);
        }
        else
        {
            throw new QWebException("404 Page Not Found",
                "<strong>/controllers/{$cName}::{$methodName}</strong> - method doesn't exists!");
        }
    }

    public function getGlobal($key, $globalName)
    {
        if (!is_null($key) && !empty($key))
        {
            return (array_key_exists($key, $this->GLOBALS[$globalName]) ? $this->GLOBALS[$globalName][$key] : NULL);            
        }
        else
        {
            return (array_key_exists($globalName, $this->GLOBALS) ? $this->GLOBALS[$globalName] : NULL);
        }
    }

    public function setGlobal($key, $value, $globalName)
    {
        if (!empty($key))
        {
            $this->GLOBALS[$globalName][$key] = $value;
        }
        else
        {
            $this->GLOBALS[$globalName][] = $value;
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
    private function loadExtension($name)
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

    private function ParseURI()
    {
        $scheme = parse_url(WEB_URL);
        $str = str_replace($scheme['path'], "", $_SERVER['REQUEST_URI']);
            
        $pos = strpos($str, "?");

        if((bool)$pos)
        {
            $str = substr($str, 0, $pos);
        }

        $parts = preg_split("/\//", $str, -1, PREG_SPLIT_NO_EMPTY);

        return array_map(create_function('$str', 'return (preg_match("/^[\w\-\.]{1,50}$/", trim($str)))?$str:NULL;'), $parts);
    }

    private function loadModule()
    {
        include_once (CORE_DIR . '/qxcore/Module.php');

        return new QModule();
    }

    // Magic Methods
    protected function __get($name)
    {
        if (isset($name) && ctype_alnum($name))
        {
            if(strtolower($name) == 'module')
            {
                return $this->$name = $this->loadModule();
            }
            
            $this->loadExtension($name);

            $qname = "Q{$name}";

            $this->$name = new $qname(self::$QXC);
            $this->$name->QXC = self::$QXC;
            $this->$name->PATH = CORE_DIR . '/system/' . strtolower($name);

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
