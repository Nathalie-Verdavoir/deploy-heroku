<?php

namespace Nat\DeployBundle\Service;

class SetOtherVars
{
    public function __construct()
    {
        $processes = [];
        foreach (explode(',', $_SERVER['SYMFONY_DOTENV_VARS']) as $envVar) {
            if ($envVar !== 'DATABASE_URL') {
                $value = $envVar . '=' . $_SERVER[$envVar];
                $processes[] = ['heroku', 'config:set', $value, '--app=' . NatInfos::getInstance()->herokuAppName];
            }
        }
        RunProcess::getInstance()->runProcesses($processes);
    }
}
