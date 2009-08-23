<?php

class QDb
{
    public $Driver = "";
    public $Server = "";
    public $Port = "";
    public $User = "";
    public $Password = "";
    public $Database = "";
    public $Encoding = "";
    public $Connection;
    public $CommandText = "";
    public $Parameters = array();
    
    private $connObject;
    
    function __construct()
    {
        // Receive main instance
        $qxc = QXC();
        
        $connStr = $qxc->Config->Get('connection_string');

        if(!empty ($connStr))
        {
            $dbArray = $this->ParseConnectionString($connStr);

            $this->Driver = $dbArray['driver'];
            $this->Server = $dbArray['server'];
            $this->Port = $dbArray['port'];
            $this->User = $dbArray['user'];
            $this->Password = $dbArray['password'];
            $this->Database = $dbArray['database'];
            $this->Encoding = $dbArray['encoding'];
        }
        else
        {
            $this->Driver = $qxc->Config->Get('driver');
            $this->Server = $qxc->Config->Get('server');
            $this->Port = $qxc->Config->Get('port');
            $this->User = $qxc->Config->Get('user');
            $this->Password = $qxc->Config->Get('password');
            $this->Database = $qxc->Config->Get('database');
        }

        $driverName = strtolower($this->Driver);

        define("USE_PDO", class_exists(PDO) && in_array(strtolower($this->Driver), PDO::getAvailableDrivers()));

        if (USE_PDO)
        {
            include_once (CORE_DIR . "/system/db/PDOBase.php");

            // Load required driver
            
            $driverPath = CORE_DIR . "/system/db/drivers/{$driverName}/{$driverName}.pdo." . CORE_PHP_EXT;

            if (file_exists($driverPath))
            {
                include_once ($driverPath);

                $className = "Q" . $this->Driver . "Driver";

                try
                {
                    $this->connObject = new $className();
                    $this->connObject->Driver = $this->Driver;
                    $this->connObject->Server = $this->Server;
                    $this->connObject->Port = $this->Server;
                    $this->connObject->User = $this->User;
                    $this->connObject->Password = $this->Password;
                    $this->connObject->Database = $this->Database;

                    $this->Connection = new QDbConnection(&$this->connObject);
                }
                catch (PDOException $e)
                {
                    pr($e->getMessage()); // TODO write custom exception realization
                }
            }
            else
            {
                throw new Exception($message, $code); // TODO write custome realization
            }
        }
        else
        {
            // TODO write else realization
        }
    }

    // Punblic Methods
    public function Query($sql)
    {    
        if(func_num_args() > 1)
        {
            $args = func_get_args();
            return $this->connObject->Query($sql, $args[1]);
        }
        else
        {
            return $this->connObject->Query($sql);
        }
    }

    public function ExecuteQuery($sql = "")
    {
        $q = (empty ($sql)) ? $this->CommandText : $sql;

        return $this->connObject->Query($q, $this->Parameters);
    }

    // Private Methods
    /**
     * @return array
     */
    private function ParseConnectionString($connStr)
    {
        $matchArray = preg_split("/;/", $connStr);

        foreach ($matchArray as $val)
        {
            list ($dbKey, $dbVal) = split("=", $val);
            
            $tmpArray[strtolower($dbKey)] = $dbVal;
        }

        return $tmpArray;
    }
}

class QDbConnection
{
    public $State;
    private $dbObject;

    function __construct($dbObject)
    {
        $this->dbObject = &$dbObject;

        $this->Open();
    }

    public function Open()
    {
        $this->dbObject->Initialize();

        $this->State = true;
    }

    public function Close()
    {
        $this->dbObject = null;

        $this->State = false;
    }
}
