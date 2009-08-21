<?php

class QMySQLDriver extends QPDODriverBase
{
    private $DSN;
    
    function __construct()
    {
              
    }

    function Initialize()
    {
        $this->DSN = $this->GenerateConnectionString();

        parent::__construct($this->DSN, $this->User, $this->Password);
    }

    public function Connect()
    {
        pr($this->DSN);
    }

    public function Disconnect()
    {
        pr($this->Driver, $terminate);
    }

    public function Query($sql)
    {
        var_dump(func_get_args());
        die;

        pr($this->DSN);
    }

    public function Clean($sql)
    {
        return $this->quote($sql);
    }
}