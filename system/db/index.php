<?php

class QDb
{
    public $Driver = "";
    public $Server = "";
    public $User = "";
    public $Password = "";
    private $pdoDrivers = array(
        'mysql',
        'mssql',
        'postgresql',
        'oracle'
    );
    
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
            $this->User = $dbArray['user'];
            $this->Password = $dbArray['password'];
        }
        else
        {
            $this->Driver = $qxc->Config->Get('driver');
            $this->Server = $qxc->Config->Get('server');
            $this->User = $qxc->Config->Get('user');
            $this->Password = $qxc->Config->Get('password');
        }
    }

    // Private Methods
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

/**
 * @return QModel Returns QModel
 */
    function archive()
    {
        echo ok;
    }

}
