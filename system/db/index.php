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
    
    private static $connObject;
    
    function __construct($qxc)
    {        
        // We need singleton object otherwise we have big overhead
        if (!(bool)self::$connObject)
        {            
            $connStr = $qxc->Config->Get('connection_string');

            if(empty ($connStr))
            {
                $this->Driver = $qxc->Config->Get('driver');
                $this->Server = $qxc->Config->Get('server');
                $this->Port = $qxc->Config->Get('port');
                $this->User = $qxc->Config->Get('user');
                $this->Password = $qxc->Config->Get('password');
                $this->Database = $qxc->Config->Get('database');
            }

            define("USE_PDO", class_exists(PDO));

            if (USE_PDO)
            {
                include_once (CORE_DIR . "/system/db/PDOBase.php");

                try
                {
                    self::$connObject = new PDOBase($connStr);

                    $this->Connection = new QDbConnection(&self::$connObject);
                }
                catch (PDOException $e)
                {
                    throw new QException("PDO Exception: " . $e->getMessage());
                }
            }
            else
            {
            // TODO write else realization
            }
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

            return self::$connObject->Query($sql, $args[1]);
        }

        if ($this->Cache)
        {
            $this->Cache = 0;
            
            return $this->GetCachedData($sql, $this->Parameters);
        }

        return self::$connObject->Query($sql, $this->Parameters);
    }

    public function ExecuteQuery($sql = "")
    {        
        $sql = (empty ($sql)) ? $this->CommandText : $sql;        

        if ($this->Cache)
        {
            $this->Cache = 0;
            
            return $this->GetCachedData($sql, $this->Parameters);
        }

        return self::$connObject->Query($sql, $this->Parameters);
    }

    public function ExecuteScalar($sql = "")
    {
        $sql = (empty ($sql)) ? $this->CommandText : $sql;

        if ($this->Cache)
        {
            $this->Cache = 0;
            
            return $this->GetCachedData($sql, $this->Parameters);
        }
        
        return self::$connObject->Query($sql, $this->Parameters, true);
    }

    public function Cache($timeout)
    {
        $this->Cache = (int)$timeout;

        return $this;
    }

    private function GetCachedData($query, $args = array())
    {
        $hash = md5($query . serialize($args));

        if(!$arr = $this->QXC->Cache->Get($hash))
        {
            $arr = self::$connObject->Query($query, $args);

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
        $this->State = true;
    }

    public function Close()
    {
        $this->dbObject = null;

        $this->State = false;
    }
}
