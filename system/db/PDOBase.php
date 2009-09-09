<?php

abstract class QPDODriverBase extends PDO
{
    private $QDB;
    private $DSN;

    function  __construct($qdb)
    {
        $this->QDB = $qdb;
        
        $this->DSN = $this->GenerateDSN();

        parent::__construct($this->DSN, $this->QDB->User, $this->QDB->Password);

        if (!empty ($this->QDB->Encoding))
        {
            $this->SetEncoding($this->QDB->Encoding);
        }
    }
    
    public function Connect()
    {
        // NULL
    }

    public function Disconnect()
    {
        // NULL
    }

    public function Query($sql)
    {
        $stmt = $this->prepare($sql);

        $args = func_get_args();

        if(is_array($args[1]))
        {
            $stmt->execute($args[1]);
        }
        else
        {
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    public function Clean($sql)
    {
        // NULL
    }

    public function SetEncoding($value = "utf8")
    {
        $this->query("SET NAMES {$value}");
    }

    /**
     * @return array
     */
    protected function GenerateDSN()
    {
        // Generate PHP PDO suitable
        $str = strtolower($this->QDB->Driver) . ":";
        $str .= empty($this->QDB->Server) ? "host=localhost;" : "host={$this->QDB->Server};";
        $str .= empty($this->QDB->Port) ? "" : "port={$this->QDB->Port};";
        $str .= empty($this->QDB->Database) ? "" : "dbname={$this->QDB->Database};";

        return $str;
    }
}