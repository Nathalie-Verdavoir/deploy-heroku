<?php

namespace Nat\DeployBundle\Service;

class AddClearDB
{
    public function __construct()
    {
        $infos = NatInfos::getInstance();
        $appName = '--app=' . $infos->herokuAppName;
        $clearDbUrl = 'CLEARDB_NAT_URL';
        $message = Message::getInstance();
        $message->getColoredMessage(['Add ClearDb and setting APP_ENV in Heroku'], 'blue');
        $processes = [
            ['heroku', 'config:get', $clearDbUrl, $appName]
        ];
        $infos->databaseUrl = $infos->natProcess->runProcesses($processes);
        if (!str_contains($infos->databaseUrl, 'm')) { // no database yet so it needs one
            $processes = [
                ['heroku', 'addons:create', 'cleardb:ignite', $appName, '--as=CLEARDB_NAT'],
                ['heroku', 'config|grep', $clearDbUrl],
            ];
            $infos->natProcess->runProcesses($processes);
            $processes = [
                ['heroku', 'config:get', $clearDbUrl, $appName]
            ];
            $infos->databaseUrl = $infos->natProcess->runProcesses($processes);
        }
        $message->getColoredMessage(['Copying CLEARDB_DATABASE_URL to DATABASE_URL in Heroku'], 'blue');
    }
}
