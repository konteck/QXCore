<?php

class QLog
{
    static private $logsArray = array();
    
    public function Trace($message)
    {
        QLog::$logsArray['trace'][] = $message;
    }

    public function Warn($message)
    {
        QLog::$logsArray['warn'][] = $message;
    }
    
    public function Error($message)
    {
        QLog::$logsArray['error'][] = $message;
    }

    public function GetLogs()
    {
        return self::$logsArray;
    }
}
