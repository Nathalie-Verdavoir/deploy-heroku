<?php 

/*
 * (c) Nathalie Verdavoir <nathalie.verdavoir@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Nat\DeployBundle\Command\Deploy;

use Nat\DeployBundle\Service\Message;
use Nat\DeployBundle\Service\RunProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

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
    private $message;

    //need form
    private $herokuUser;
    private $herokuApiKey;
    private $herokuAppName;
    private $databaseNeeded;


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->progressStart(100);

        $this->createHtaccess();

        $this->io->progressAdvance(10);

        $this->createEnvPhp();

        $this->io->progressAdvance(10);

        $this->createProcfile();

        $this->io->progressAdvance(10);

        $this->checkForHerokuLogin();

        $this->io->progressAdvance(10);
        
        $this->setAppEnvProd();

        $this->io->progressAdvance(5);

        if( $this->databaseNeeded == 'yes' ){
            $this->setClearDbAddon();
        }

        $this->io->progressAdvance();

        if( $this->appSecret ){
            $this->setAppSecret();
        }

        $this->io->progressAdvance();

        if(count($this->otherVars)>0){
            $this->setOtherVars();
        }

        $this->io->progressFinish(100);

        $this->message->getColoredMessage('WOAH ! It seems that everything is done ! Enjoy !', 'green');
    
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
        $this->message->getColoredMessage([
            'Hello, I am tool to deploy your symfony project on Heroku',
            'developed by Nathalie Verdavoir nathalie.verdavoir@laposte.net',
            ], 
            'blue'
        );
        $this->message->getColoredMessage([
            'I will help you step by step',
            '1-You must have an Heroku account'
            ], 
            'cyan'
        );
        $this->message->getColoredMessage([
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
        $this->message = new Message($this->input, $this->output);
        $this->natProcess = new RunProcess($this->input, $this->output, $this->io);
    }

    private function createHtaccess()
    {
        $this->message->getColoredMessage('Creating .htaccess', 'blue');
        /*$processes = [
            ['composer', 'remove', 'symfony/apache-pack', ''],
            ['composer', 'config', 'extra.symfony.allow-contrib', true],
            ['composer', 'up'],
            ['composer', 'require', 'symfony/apache-pack', ''],
            ['composer', 'up'],
            ['composer', 'config', 'extra.symfony.allow-contrib', ''],
            ['composer', 'up']
        ];
        $this->natProcess->runProcesses($processes);
        */
        $this->filesystem->mirror('/vendor/nat/deploy/public', '/public');
        $this->message->getColoredMessage('.htaccess done!', 'green');
    }

    private function createEnvPhp()
    {
        $this->message->getColoredMessage('Creating .env.php file', 'blue');
        $processes = [
            ['composer', 'dump-env', 'prod'],
        ];
        $this->natProcess->runProcesses($processes);
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
        $this->message->getColoredMessage('.env.php done!', 'green');
    }

    private function createProcfile()
    {
        $this->message->getColoredMessage('Creating Procfile', 'blue'); 
        $this->filesystem->dumpFile('Procfile', 'web: heroku-php-apache2 public/');
        $this->message->getColoredMessage('Procfile done!', 'green');
    }

    private function checkForHerokuLogin()
    {
        $this->message->getColoredMessage(['Login to Heroku','Waiting for you to log in browser'], 'blue');
        $processes = [
            ['heroku', 'authorizations:create'],
            ['heroku', 'auth:whoami'],
        ];
        $paramsProc = [$this->herokuUser, $this->herokuApiKey];
        $this->natProcess->runProcesses($processes, $paramsProc);
        $this->message->getColoredMessage('Logged in !', 'green');
    }

    private function setAppEnvProd()
    {
        $this->message->getColoredMessage(['Setting APP_ENV in Heroku'], 'blue');
        $processes = [
            ['heroku', 'config:set', 'APP_ENV=prod', '--app='.$this->herokuAppName]
        ];
        $this->natProcess->runProcesses($processes);
        $this->message->getColoredMessage(['APP_ENV set'], 'green');
    }

    private function setClearDbAddon()
    {
        $this->message->getColoredMessage(['Add ClearDb and setting APP_ENV in Heroku'], 'blue');
        $processes = [
            ['heroku', 'config:get', 'CLEARDB_DATABASE_URL', '--app='.$this->herokuAppName]
        ];
        $clearDB = $this->natProcess->runProcesses($processes);
        if(!str_contains($clearDB, 'm')){ // no database yet so it needs one
            $processes = [
                ['heroku', 'addons:create', 'cleardb:ignite', '--app='.$this->herokuAppName],
                ['heroku', 'config|grep', 'CLEARDB_DATABASE_URL'],
            ];
            $this->natProcess->runProcesses($processes); 
            $processes = [
                ['heroku', 'config:get', 'CLEARDB_DATABASE_URL', '--app='.$this->herokuAppName]
            ];
            $clearDB = $this->natProcess->runProcesses($processes);
        }
        $this->message->getColoredMessage(['Copying CLEARDB_DATABASE_URL to DATABASE_URL in Heroku'], 'blue');
        $databasevar = 'DATABASE_URL='. $this->clean($clearDB);

        $processes = [
            ['heroku', 'config:set', $databasevar, '--app='.$this->herokuAppName]
        ];
        $this->natProcess->runProcesses($processes);
        $this->message->getColoredMessage(['ClearDb added and DATABASE_URL set'], 'green');
    }

    private function clean($text)
    {
        $text = trim( preg_replace( '/\s+/', ' ', $text ) );  
        $text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $text);
        return $text;
    }

    private function setAppSecret()
    {
        $this->message->getColoredMessage(['Setting APP_SECRET in Heroku'], 'blue');
        $processes = [
            ['heroku', 'config:set', 'APP_SECRET='.$this->appSecret, '--app='.$this->herokuAppName],
        ];
        $this->natProcess->runProcesses($processes);
        $this->message->getColoredMessage(['APP_SECRET set in Heroku'], 'green');
    }

    private function setOtherVars()
    {
        $processes = [];
        foreach($this->otherVars as $envVar){
            $this->message->getColoredMessage(['Setting '. $envVar. ' in Heroku'], 'blue');
            $processes[] = ['heroku', 'config:set', $envVar . '=' . $_SERVER[$envVar], '--app='.$this->herokuAppName];
            $this->message->getColoredMessage([$envVar. ' set in Heroku'], 'green');
        }
    }
}
