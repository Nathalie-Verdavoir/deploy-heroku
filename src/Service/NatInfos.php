<?php

namespace Nat\DeployBundle\Service;

class NatInfos
{
    //singleton

    public $input;
    public $output;
    public $herokuUser;
    public $herokuApiKey;
    public $herokuAppName;
    public $databaseNeeded;
    public $databaseUrl;
    public $natProcess;
    public $io;

    private static $_instance;

    //to call the unique instance of this class form anywhere else you have to use : NatInfos::getInstance();

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            return self::$_instance = new NatInfos();
        }
        return self::$_instance;
    }

    public function __construct()
    {
    }
}
