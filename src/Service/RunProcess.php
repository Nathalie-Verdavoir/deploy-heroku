<?php

namespace Nat\DeployBundle\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RunProcess
{
    private $io;
    private $input;
    private $output;
    private $message;
    private static $_instance;

    //to call the unique instance of this class form anywhere else you have to use : RunProcess::getInstance();

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            return self::$_instance = new RunProcess();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $infos = NatInfos::getInstance();
        $this->input = $infos->input;
        $this->output = $infos->output;
        $this->io = $infos->io;
        $this->message = Message::getInstance();
    }

    public function runProcesses($processes, array $paramsProc = [])
    {
        foreach ($processes as $proc) {
            $process = new Process($proc);
            if ($proc[1] === 'config:set') {
                $this->message->getColoredMessage(['Setting ' . substr($proc[2], 0, strpos($proc[2], '=')) . ' in Heroku'], 'blue');
            }
            $process->setTimeout(3600);
            try {

                //FOR HEROKU LOGIN ONLY 
                if ($proc[1] == "authorizations:create") {
                    $process->setInput($paramsProc[0], $paramsProc[1]);
                }

                $process->mustRun();
                $this->getProcessMessages($proc);

                //FOR CLEARDB_DATABASE_URL ONLY 
                if ($proc[1] === 'config:get' && $proc[2] === 'CLEARDB_DATABASE_URL') {
                    if (!str_contains($process->getOutput(), 'm')) {
                        echo 'Adding a new ClearDb database...';
                    } else {
                        echo 'Your CLEARDB database url : ' . $process->getOutput();
                    }
                    return $process->getOutput();
                }
                if ($proc[1] === 'config:set') {
                    $this->message->getColoredMessage([substr($proc[2], 0, strpos($proc[2], '=')) . ' set in Heroku'], 'green');
                    $this->io->progressAdvance(5);
                }
                echo $process->getOutput();
            } catch (ProcessFailedException $exception) {
                echo $exception->getMessage();

                //FOR HEROKU AUTH FAILED ONLY
                if ($proc[1] == "authorizations:create") {
                    $this->message->getColoredMessage(['Please run \'heroku login -i\' command'], 'red');
                }
            }
        }
    }

    private function getProcessMessages(array $proc)
    {
        $this->output->writeln(
            [
                '<info>' . implode(' ', $proc) . '</>',
                ''
            ]
        );
        $this->io->progressAdvance(2);
        $this->output->writeln(['']);
    }
}
