<?php

namespace console\helpers;

class Logger
{

    private $root;
    private $channelName;
    private $message;
    private $logFile;
    private $subPathLog = '/runtime/logs/';
    private $exts = [
        'log' => 'log',
        'txt' => 'txt'
    ];

    public function __construct($channelName)
    {

        $this->root = dirname(__DIR__);
        $this->channelName = $channelName;
        $this->logFile = $this->root . "{$this->subPathLog}{$channelName}.{$this->exts['log']}";


    }

    public function setLog($message)
    {
        file_put_contents($this->logFile, $message, FILE_APPEND);
        if (substr(php_uname(), 0, 7) !== "Windows") {
            chmod($this->logFile, 0777);
        }
    }

    public function getLogs()
    {
        return file_get_contents($this->logFile);
    }


}



