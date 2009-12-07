<?php

class PDOBase extends PDO
{
    private $QDB;
    private $DSN;

    function  __construct($dsn)
    {        
        $this->DSN = $dsn;

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

    public function Query($sql, $params = array(), $single = false)
    {
        try
        {
            $stmt = $this->prepare($sql);

            if(is_array($params) && count($params) > 0)
            {
                $stmt->execute($params);
            }
            else
            {
                $stmt->execute();
            }

            if ($single)
            {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else
            {
                 return $stmt->fetchAll();
            }
        }
        catch (PDOException $e)
        {
            throw new QException("PDO Exception: " . $e->getMessage());
        }
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
     * TODO: Remove, not needed anymore
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