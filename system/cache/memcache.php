<?php

if (!class_exists("Memcache"))
{
    throw new QException("Memcache supported disabled on this server!");
}

class QMemcache
{
    private $memcache;
    private $host;
    private $port;
    
    function __construct($host = "127.0.0.1", $port = 11211, $persistance = false)
    {
        $this->host = $host;
        $this->port = $port;
        
        $this->memcache = new Memcache();

        if($persistance)
            $this->memcache->pconnect($this->host, $this->port) or die ("Could not connect to Memcache server");
        else
            $this->memcache->connect($this->host, $this->port) or die ("Could not connect to Memcache server");
    }

    public function Get($key)
    {
        return $this->memcache->get($key);
    }

    public function Set($key, $value, $expire)
    {
        $this->memcache->set($key, $value, false, $expire) or die ("Failed to save data at the server");

        return true;
    }

    public function Del($key)
    {
        $this->memcache->delete($key);

        return true;
    }

    public function GetVersion()
    {
        return $this->memcache->getVersion();;
    }

    public function Flush()
    {
        $this->memcache->flush();

        return true;
    }
}