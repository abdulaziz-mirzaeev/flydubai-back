<?php

namespace console\helpers;

use yii\helpers\ArrayHelper;

class Process
{
    #region Vars
    private $pid;
    public $command;
    private $root;
    private $php = '/usr/bin/php';
    private $logger;
    #endVars

    #region Main
    public function __construct($cl = false)
    {

        //root of the project
        $this->root = dirname(dirname(__DIR__));

        $this->logger = new Logger('Process');

        if (!$this->isLinux()) {
            $this->php = "C:/localserver/php-7.3/php.exe";
        }

        $this->command = $this->php . ' ' . $this->root . '/yii' . ' notify-fly/notify';

        if ($cl != false) {
            $this->command = $cl;
        }

    }

    private function runCom()
    {

        $command = $this->command;

        //$command = 'nohup ' . $this->command . ' > /dev/null 2>&1 & echo $!';

        $this->logger->setLog($command . 'COMMAND' . PHP_EOL);

        //exec($command, $op);

        $op = $this->exec($command);

        //exec($command, $op);

        if (is_array($op)) {
            $this->pid = (int)ArrayHelper::getValue($op, 0);
            $this->logger->setLog($this->pid . PHP_EOL . "PID");
        }

    }

    public function exec($cmd)
    {

        if ($this->isLinux()) {
            exec($cmd . " > /dev/null &", $op);
            return $op;
        }

        pclose(popen("start /B {$cmd}", "r"));

    }


    #endMain

    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function status()
    {

        $command = 'ps -p ' . $this->pid;

        $this->logger->setLog($command . __METHOD__);

        exec($command, $op);

        if (!isset($op[1])) return false;

        else return true;


    }

    public function start()
    {

        $this->logger->setLog($this->command . __METHOD__);

        if ($this->command != '') $this->runCom();
        else return true;

    }

    public function stop()
    {

        $command = 'kill ' . $this->pid;

        $this->logger->setLog($command . __METHOD__);

        exec($command);

        if ($this->status() == false) return true;
        else return false;

    }

    /**
     * @return bool
     */
    public function isLinux()
    {

        if (substr(php_uname(), 0, 7) == "Windows") {
            return false;
        }

        return true;

    }


    public function phpTasks()
    {

        $command = "tasklist /v find php exit";

        if ($this->isLinux()) {
            $command = "ps aux | grep php exit";
        }

        exec($command, $output);

        return $output;

    }

}



