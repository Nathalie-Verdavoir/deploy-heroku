<?php

namespace Nat\DeployBundle\Service;

class SetClearDbAddon
{
    public function __construct()
    {
        $infos = NatInfos::getInstance();
        $databasevar = 'DATABASE_URL=' . $this->clean($infos->databaseUrl);
        $processes = [
            ['heroku', 'config:set', $databasevar, '--app=' . $infos->herokuAppName]
        ];
        $infos->natProcess->runProcesses($processes);
        Message::getInstance()->getColoredMessage(['ClearDb added and DATABASE_URL set'], 'green');
    }

    private function clean($text)
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        $text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $text);
        return $text;
    }
}
