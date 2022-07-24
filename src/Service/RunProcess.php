<?php

namespace Nat\DeployBundle\Service;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RunProcess
{
    private $io;
    private $input;
    private $output;
    private $message;

    public function __construct(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = $io;
        $this->message = new Message($this->input, $this->output);
    }
    
    public function runProcesses($processes, array $paramsProc = [])
    {
        foreach($processes as $proc){
            $process = new Process($proc);
            try {
                
                //FOR HEROKU LOGIN ONLY 
                if($proc[1]=="authorizations:create") {
                    $process->setInput($herokuUser = $paramsProc[0], $herokuApiKey = $paramsProc[1]);
                }

                $process->mustRun();
                $this->getProcessMessages($proc);
                
                //FOR CLEARDB_DATABASE_URL ONLY 
                if($proc[1]==='config:get' && $proc[2]==='CLEARDB_DATABASE_URL') {     
                   if(!str_contains($process->getOutput(), 'm')){
                        echo 'Adding a new ClearDb database...';
                    }else{
                        echo 'Your CLEARDB database url : ' . $process->getOutput();
                    }
                    return $process->getOutput();
                }

                echo $process->getOutput();
            } catch (ProcessFailedException $exception) {
                echo $exception->getMessage();

                //FOR HEROKU AUTH FAILED ONLY
                if($proc[1]=="authorizations:create") {
                    $this->message->getColoredMessage(['Please run \'heroku login -i\' command'], 'red');
                }
            } 
        }
    }

    private function getProcessMessages(array $proc)
    {
        //echo  '$this->output';
        $this->output->writeln(
            [ '<info>' . implode(' ',$proc) . '</>',
            '']
        );
        $this->io->progressAdvance(2);
        $this->output->writeln(['']);
    }

}