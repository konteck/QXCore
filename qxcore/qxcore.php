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

define('CORE_CONFIG_NAME', 'web');

define('CORE_CONFIG_EXTENSION', 'config.xml');

define('CORE_MAIN_CONTROLLER', 'Main');

define('CORE_VER', '0.92');

//--

require_once (CORE_DIR . '/qxcore/Controller.php');
require_once (CORE_DIR . '/qxcore/ClassDefination.php');
require_once (CORE_DIR . '/qxcore/Core.Config.php');
require_once (CORE_DIR . '/qxcore/xhtml.php');

if (! function_exists('pr'))
{
    /**
     * For debugging purprouses
     */
    function pr($object, $terminate = false)
    {
        echo("<pre>");

//        if (is_array($object))
//        {
//            foreach ($object as $var)
//            {
//                var_dump($var, false);
//            }
//        }
//        else
//        {
            var_dump($object);
//        }

        if ($terminate)
        {
            die("Exec: " . ET());
        }

        echo("</pre>");
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
    private static $_QXC;
    private $_GLOBALS;

    public $test = 'test';

    public function test()
    {
        return $this->test;
    }

    function __construct()
    {
        global $urlRewrite; // TODO remove

        $this->_GLOBALS = array();

        $this->_GLOBALS['POST'] = $_POST;
        $this->_GLOBALS['GET'] = $_GET;
        $this->_GLOBALS['COOKIE'] = $_COOKIE;
        $this->_GLOBALS['SESSION'] = $_SESSION;
        $this->_GLOBALS['FILES'] = $_FILES;

        if ($urlRewrite == true && ! empty($_GET['qstring']))
        {
            $this->_GLOBALS['QSTRING'] = array_map(create_function('$str', 'return (preg_match("/^[a-z0-9\_\-]{1,50}$/", $str))?$str:NULL;'), split("/", $_GET['qstring']));

            unset($this->_GLOBALS['GET']['qstring']);
        }

        $_GET = $_POST = $_REQUEST = $_COOKIE = $_SESSION = $_FILES = array();        
    }

    public function Initialize()
    {
        // Is in Debug mode
        define("DEBUG", (bool)$this->Config->Get('debug'));
        
        // Load necessary controllers
        $this->LoadController();
    }

    public function LoadController()
    {
        global $urlRewrite;

        $cName = CORE_MAIN_CONTROLLER;

        if ($urlRewrite && !empty($this->_GLOBALS['QSTRING']))
        {
            $cName = $this->getPart(0);
        }

        $cName = (is_null($cName) || empty ($cName))?CORE_MAIN_CONTROLLER:strtolower(trim($cName));

        include_once (APP_DIR . "/controllers/{$cName}." . CORE_PHP_EXT);

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
        if (ctype_digit($num))
        {
            return $this->_GLOBALS['QSTRING'];
        }
        else
        {
            return $this->_GLOBALS['QSTRING'][$num];
        }
    }

    public static function GetInstance()
    {
        if (!is_object(QXCore::$_QXC))
        {
            QXCore::$_QXC = new QXCore();
        }
        
        return QXCore::$_QXC;
    }

    // Private Methods
    private function LoadModule($name)
    {
        $extPath = CORE_DIR . '/system/' . strtolower($name) . '/index.php';

        if (file_exists($extPath))
        {
            include_once ($extPath);
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
            $this->$name->QXC = $this;

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
