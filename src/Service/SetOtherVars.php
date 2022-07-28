<?php

namespace Nat\DeployBundle\Service;

class SetOtherVars
{
    public function __construct($input, $output, $io, $herokuAppName)
    {
        $processes = [];
        foreach (explode(',', $_SERVER['SYMFONY_DOTENV_VARS']) as $envVar) {
            if ($envVar !== 'DATABASE_URL') {
                $value = $envVar . '=' . $_SERVER[$envVar];
                $processes[] = ['heroku', 'config:set', $value, '--app=' . $herokuAppName];
            }
        }
        RunProcess::getInstance($input, $output, $io)->runProcesses($processes);
    }
}
