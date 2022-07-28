<?php

namespace Nat\DeployBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NatDeployBundle extends Bundle
{
    private static $_instance;

    //to call the unique instance of this class form anywhere else you have to use : NatDeployBundle::getInstance();

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            return self::$_instance = new NatDeployBundle();
        }
        return self::$_instance;
    }

    public function getDir()
    {
        return \dirname($this->getPath());
    }

    public function __construct()
    {
    }
}
