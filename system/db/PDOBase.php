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

            $result = $stmt->errorInfo();

            if ($result[1] > 0)
            {
                throw new QException($result[2]);
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
}