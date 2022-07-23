<?php 

/*
 * (c) Nathalie Verdavoir <nathalie.verdavoir@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

// If you don't to add a custom vendor folder, then use the simple class
// namespace HerokuDeploy;
namespace Nat\DeployBundle\Command\Deploy;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'nat:heroku',
    description: 'Prepare To Deploy On Heroku.',
    hidden: false,
    aliases: ['nat:h']
)]
class Deploy extends Command
{
    private $io;
    private $filesystem;
    private $input;
    private $output;
    private $appSecret;
    private $otherVars;
    private $clearDB;

    //need form
    private $herokuUser;
    private $herokuApiKey;
    private $herokuAppName;
    private $databaseNeeded;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->progressStart(100);

        $this->createHtaccess();

        $this->io->progressAdvance(20);

        $this->createEnvPhp();

        $this->io->progressAdvance(20);

        $this->createProcfile();

        $this->io->progressAdvance(10);

        $this->checkForHerokuLogin();

        $this->io->progressAdvance(10);
        
        $this->setAppEnvProd();

        $this->io->progressAdvance(5);

        if( $this->databaseNeeded == 'yes' ){
            $this->setClearDbAddon();
        }

        if( $this->appSecret ){
            $this->setAppSecret();
        }

        if(count($this->otherVars)>0){
            $this->setOtherVars();
        }

        $this->io->progressFinish(100);

        // outputs a message followed by a "\n"
        $this->getColoredMessage('WOAH ! It seems that everything is done ! Enjoy !', 'green');
    
        return Command::SUCCESS;
    }

/**
     * Interacts with the user.
     *
     * This method is executed before the InputDefinition is validated.
     * This means that this is the only place where the command can
     * interactively ask for values of missing required arguments.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->getColoredMessage([
            'Hello, I am tool to deploy your symfony project on Heroku',
            'developed by Nathalie Verdavoir nathalie.verdavoir@laposte.net',
            ], 
            'blue'
        );
        $this->getColoredMessage([
            'I will help you step by step',
            '1-You must have an Heroku account'
            ], 
            'cyan'
        );
        $this->getColoredMessage([
            'If you need a DATABASE (mySql/MariaDb) for your app',
            '2-You must have a credit card associated with your Heroku account',
            '(don\'t worry it is totally FREE if you keep default settings)',
            'https://dashboard.heroku.com/account/billing'
            ], 
            'cyan'
        );

        
        $this->herokuUser = $this->io->ask('What is your Username to log in Heroku Account? (your.email@example.com)', '',function ($username) {
            if (empty($username)) {
                throw new \RuntimeException('Username (email) cannot be empty.');
            }
            return $username;
        });
        $this->herokuApiKey = $this->io->ask('What is your ApiKey in Heroku Account?', '', function ($apiKey) {
            if (empty($apiKey)) {
                throw new \RuntimeException('Password cannot be empty.');
            }
            return $apiKey;
        });

        $this->herokuAppName = $this->io->ask('What is the name of your app? (app-example-name)', '',function ($appName) {
            if (empty($appName)) {
                throw new \RuntimeException('AppName cannot be empty.');
            }
            return $appName;
        });
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Do you need a ClearDb/mySql addon (set to free, ignite plan)?',
            // choices can also be PHP objects that implement __toString() method
            ['yes', 'no'],
            0
        );
        $this->databaseNeeded = $helper->ask($input, $output, $question);
        $output->writeln('You have just selected: '.$this->databaseNeeded);
        
    }


/**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @see InputInterface::bind()
     * @see InputInterface::validate()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($this->input, $this->output);
        $this->filesystem = new Filesystem();
        $this->processes = [];
        $this->otherVars = [];
    }

    private function createHtaccess()
    {
        $this->getColoredMessage('Creating .htaccess', 'blue');
        $processes = [
            ['composer', 'remove', 'symfony/apache-pack', ''],
            ['composer', 'config', 'extra.symfony.allow-contrib', true],
            ['composer', 'up'],
            ['composer', 'require', 'symfony/apache-pack', ''],
            ['composer', 'up'],
            ['composer', 'config', 'extra.symfony.allow-contrib', ''],
            ['composer', 'up']
        ];
        $this->runProcesses($processes);
        $this->getColoredMessage('.htaccess done!', 'green');
    }

    private function createEnvPhp()
    {
        $this->getColoredMessage('Creating .env.php file', 'blue');
        $processes = [
            ['composer', 'dump-env', 'prod'],
        ];
        $this->runProcesses($processes);
        $this->filesystem->copy('.env.local.php', '.env.php');
        $this->filesystem->dumpFile('.env.php', "<?php

        return array (
          'APP_ENV' => 'prod',
        );
        ");
        foreach (explode(',', $_SERVER['SYMFONY_DOTENV_VARS']) as $parm) {
            $this->output->writeln([$parm. '='.$_SERVER[$parm]]);
            if($parm=='DATABASE_URL') {
                $this->databaseUrl =  $_SERVER[$parm];
            }else if($parm=='APP_SECRET') {
                $this->appSecret = $_SERVER[$parm];
            }else{
                $this->otherVars[] = $parm;
            }
       } 
        $this->getColoredMessage('.env.php done!', 'green');
    }

    private function createProcfile()
    {
        $this->getColoredMessage('Creating Procfile', 'blue'); 
        $this->filesystem->dumpFile('Procfile', 'web: heroku-php-apache2 public/');
        $this->getColoredMessage('Procfile done!', 'green');
    }

    private function getColoredMessage(string|array $message, string $color)
    {
        $lignes = [];
        
            $lignes[] = '';
            $lignes[] = '<bg='. $color .'>  ============================================================================  ';
            if( is_array($message) ) {
                foreach($message as $mes){
                    $separator = str_repeat(' ',71-strlen($mes));
                    $lignes[] = '  |   ' . $mes . $separator . '|  ';
                }
            }else{
                $separator = str_repeat(' ',71-strlen($message));
                $lignes[] = '  |   ' . $message . $separator . '|  ';
            }
            $lignes[] = '  ======================================================================<bg=bright-magenta>by Nat</>  </>';
            $lignes[] = '';
        $this->output->writeln($lignes);
    }

    private function runProcesses($processes)
    {
        foreach($processes as $proc){
            $process = new Process($proc);
            try {
                
                //FOR HEROKU LOGIN ONLY 
                if($proc[1]=="authorizations:create") {
                    $process->setInput($this->herokuUser,$this->herokuApiKey);
                }

                $process->mustRun();
                $this->getProcessMessages($proc);
                
                //FOR CLEARDB_DATABASE_URL ONLY 
                if($proc[1]==='config:get' && $proc[2]==='CLEARDB_DATABASE_URL') {      
                    $this->clearDB = $process->getOutput();
                }

                echo $process->getOutput();
            } catch (ProcessFailedException $exception) {
                echo $exception->getMessage();

                //FOR HEROKU AUTH FAILED ONLY
                if($proc[1]=="authorizations:create") {
                    $this->getColoredMessage(['Please run \'heroku login -i\' command'], 'red');
                }
            } 
        }
    }

    private function getProcessMessages(array $proc)
    {
        $this->output->writeln(
            [ '<info>' . implode(' ',$proc) . '</>',
            '']
        );
        $this->io->progressAdvance(2);
        $this->output->writeln(['']);
    }

    private function checkForHerokuLogin()
    {
        $this->getColoredMessage(['Login to Heroku','Waiting for you to log in browser'], 'blue');
        $processes = [
            ['heroku', 'authorizations:create'],
            ['heroku', 'auth:whoami'],
        ];
        $this->runProcesses($processes);
        $this->getColoredMessage('Logged in !', 'green');
    }

    private function setAppEnvProd()
    {
        $this->getColoredMessage(['Setting APP_ENV in Heroku'], 'blue');
        $processes = [
            ['heroku', 'config:set', 'APP_ENV=prod', '--app='.$this->herokuAppName]
        ];
        $this->runProcesses($processes);
        $this->getColoredMessage(['APP_ENV set'], 'green');
    }

    private function setClearDbAddon()
    {
        $this->getColoredMessage(['Add ClearDb and setting APP_ENV in Heroku'], 'blue');
        $processes = [
            ['heroku', 'addons:create', 'cleardb:ignite', '--app='.$this->herokuAppName],
            ['heroku', 'config|grep', 'CLEARDB_DATABASE_URL'],
            ['heroku', 'config:get', 'CLEARDB_DATABASE_URL', '--app='.$this->herokuAppName]
        ];
        $this->runProcesses($processes);
        $this->getColoredMessage(['Copying CLEARDB_DATABASE_URL to DATABASE_URL in Heroku'], 'blue');
        $databasevar = 'DATABASE_URL='.$this->clean($this->clearDB);

        $processes = [
            ['heroku', 'config:set', $databasevar, '--app='.$this->herokuAppName]
        ];
        $this->runProcesses($processes);
        $this->getColoredMessage(['ClearDb added and DATABASE_URL set'], 'green');
    }

    private function clean($text)
    {
        $text = trim( preg_replace( '/\s+/', ' ', $text ) );  
        $text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $text);
        return $text;
    }

    private function setAppSecret()
    {
        $this->getColoredMessage(['Setting APP_SECRET in Heroku'], 'blue');
        $processes = [
            ['heroku', 'config:set', 'APP_SECRET='.$this->appSecret, '--app='.$this->herokuAppName],
        ];
        $this->runProcesses($processes);
        $this->getColoredMessage(['APP_SECRET set in Heroku'], 'green');
    }

    private function setOtherVars()
    {
        $processes = [];
        foreach($this->otherVars as $envVar){
            $this->getColoredMessage(['Setting '. $envVar. ' in Heroku'], 'blue');
            $processes[] = ['heroku', 'config:set', $envVar.'='.$this->appSecret, '--app='.$this->herokuAppName];
            $this->getColoredMessage([$envVar. ' set in Heroku'], 'green');
        }
    }
}
