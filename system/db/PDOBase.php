<?php

abstract class QPDODriverBase extends PDO
{
    public $Driver;
    public $Server;
    public $Port;
    public $User;
    public $Password;
    public $Database;
    public $Encoding;
    public $Connection;    
    private $DSN;

    public function Initialize()
    {
        $this->DSN = $this->GenerateDSN();

        parent::__construct($this->DSN, $this->User, $this->Password);

        if (!empty ($this->Encoding))
        {
            $this->SetEncoding($this->Encoding);
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
        $str = strtolower($this->Driver) . ":";
        $str .= empty($this->Server) ? "host=localhost;" : "host={$this->Server};";
        $str .= empty($this->Port) ? "" : "port={$this->Port};";
        $str .= empty($this->Database) ? "" : "dbname={$this->Database};";

        return $str;
    }
}

?>