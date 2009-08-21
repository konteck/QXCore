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
    
    // Abstract Methods
    abstract function Initialize();
    
    abstract function Connect();

    public function Disconnect()
    {
        //
    }

    abstract function Clean($sql);

    public function SetEncoding($value = "utf8")
    {
        $this->query("SET NAMES {$value}");
    }

    /**
     * @return array
     */
    protected function GenerateConnectionString()
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