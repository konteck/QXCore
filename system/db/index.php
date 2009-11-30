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
    public $Cache = null;
    
    private $connObject;
    
    function __construct($qxc)
    {        
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
                    $this->connObject = new $className($this);

                    $this->Connection = new QDbConnection(&$this->connObject);
                }
                catch (PDOException $e)
                {
                    throw new QException($e->getMessage());
                }
            }
            else
            {
                throw new QException($message, $code); // TODO write custome realization
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

            if ($this->Cache)
            {
                $this->Cache = 0;
                
                return $this->GetCachedData($sql, $args[1]);
            }

            return $this->connObject->Query($sql, $args[1]);
        }

        if ($this->Cache)
        {
            $this->Cache = 0;
            
            return $this->GetCachedData($sql, $this->Parameters);
        }

        return $this->connObject->Query($sql, $this->Parameters);
    }

    public function ExecuteQuery($sql = "")
    {        
        $sql = (empty ($sql)) ? $this->CommandText : $sql;        

        if ($this->Cache)
        {
            $this->Cache = 0;
            
            return $this->GetCachedData($sql, $this->Parameters);
        }

        return $this->connObject->Query($sql, $this->Parameters);
    }

    public function ExecuteScalar($sql = "")
    {
        $sql = (empty ($sql)) ? $this->CommandText : $sql;

        if ($this->Cache)
        {
            $this->Cache = 0;
            
            return $this->GetCachedData($sql, $this->Parameters);
        }
        
        return $this->connObject->Query($sql, $this->Parameters, true);
    }

    public function Cache($timeout)
    {
        $this->Cache = (int)$timeout;

        return $this;
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

    private function GetCachedData($query, $args = array())
    {
        $hash = md5($query . serialize($args));

        if(!$arr = $this->QXC->Cache->Get($hash))
        {
            $arr = $this->connObject->Query($query, $args);

            $this->QXC->Cache->Set($hash, $arr, $this->Cache);
        }

        return $arr;
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
    //        $this->dbObject->Initialize();

        $this->State = true;
    }

    public function Close()
    {
        $this->dbObject = null;

        $this->State = false;
    }
}
