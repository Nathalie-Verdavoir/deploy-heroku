<?php

namespace Nat\DeployBundle\Service;

class CheckForHerokuLogin
{
    public function __construct()
    {
        $message = Message::getInstance();
        $message->getColoredMessage(['Login to Heroku', 'Waiting for you to log in browser'], 'blue');
        $processes = [
            ['heroku', 'authorizations:create'],
            ['heroku', 'auth:whoami'],
        ];
        $infos = NatInfos::getInstance();
        $paramsProc = [$infos->herokuUser, $infos->herokuApiKey];
        $infos->natProcess->runProcesses($processes, $paramsProc);
        $message->getColoredMessage('Logged in !', 'green');
    }
}
