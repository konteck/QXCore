<?php

class QCache
{
    private $inst;
    
    function __construct()
    {
        include_once CORE_DIR . "/system/cache/memcache.php";

        $this->inst = new QMemcache();
    }

    public function Get($key)
    {
        return $this->inst->Get($key);
    }

    public function Set($key, $value, $expire = 0)
    {
        return $this->inst->Set($key, $value, $expire);
    }

    public function Del($key)
    {
        return $this->inst->Del($key);
    }

    public function Flush()
    {
        return $this->inst->Flush();
    }
}