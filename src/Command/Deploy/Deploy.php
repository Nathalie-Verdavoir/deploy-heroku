<?php

/*
 * (c) Nathalie Verdavoir <nathalie.verdavoir@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Nat\DeployBundle\Command\Deploy;

use Nat\DeployBundle\Service\AddClearDB;
use Nat\DeployBundle\Service\CheckForHerokuLogin;
use Nat\DeployBundle\Service\CreateEnvPhp;
use Nat\DeployBundle\Service\CreateHtaccess;
use Nat\DeployBundle\Service\CreateProcfile;
use Nat\DeployBundle\Service\Message;
use Nat\DeployBundle\Service\NatInfos;
use Nat\DeployBundle\Service\RunProcess;
use Nat\DeployBundle\Service\SetClearDbAddon;
use Nat\DeployBundle\Service\SetOtherVars;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

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
    private $message;
    private $infos;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->infos->io->progressStart(100);

        new CreateHtaccess();

        new CreateEnvPhp();

        new CreateProcfile();

        new CheckForHerokuLogin();

        if ($this->infos->databaseNeeded == 'yes') {
            new AddClearDB();
            new SetClearDbAddon();
        }

        $this->infos->io->progressAdvance(5);

        new SetOtherVars();

        $this->infos->io->progressFinish(100);

        $this->message->getColoredMessage([
            'WOAH ! It seems that everything is done ! Enjoy !',
            'Please check the whole list :',
            '- [x] .htaccess is in public directory',
            '- [x] .env.php is at root of you project',
            '- [x] Procfile is at root as well',
            '- [x] ClearDb is enabled in Heroku Resources',
            '- [x] All of the vars are set in Heroku Settings (reveal config vars)',
            'Now you can export your local database to import it in you clearDb',
            '(adobe mysql workbench is fine to do it)',
            'Push your files on your github (and on Heroku if you need and deploy).',
            'Everything is ok ? You can remove me : "composer remove nat/deploy"'
        ], 'green');

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
        $this->message->getColoredMessage(
            [
                'Hello, I am tool to deploy your symfony project on Heroku',
                'developed by Nathalie Verdavoir nathalie.verdavoir@laposte.net',
            ],
            'blue'
        );
        $this->message->getColoredMessage(
            [
                'I will help you step by step',
                '1-You must have an Heroku account'
            ],
            'cyan'
        );
        $this->message->getColoredMessage(
            [
                'If you need a DATABASE (mySql/MariaDb) for your app',
                '2-You must have a credit card associated with your Heroku account',
                '(don\'t worry it is totally FREE if you keep default settings)',
                'https://dashboard.heroku.com/account/billing'
            ],
            'cyan'
        );


        $this->infos->herokuUser = $this->infos->io->ask('What is your Username to log in Heroku Account? (your.email@example.com)', '', function ($username) {
            if (empty($username)) {
                throw new \RuntimeException('Username (email) cannot be empty.');
            }
            return $username;
        });
        $this->infos->herokuApiKey = $this->infos->io->ask('What is your ApiKey in Heroku Account?', '', function ($apiKey) {
            if (empty($apiKey)) {
                throw new \RuntimeException('Password cannot be empty.');
            }
            return $apiKey;
        });

        $this->infos->herokuAppName = $this->infos->io->ask('What is the name of your app? (app-example-name)', '', function ($appName) {
            if (empty($appName)) {
                throw new \RuntimeException('AppName cannot be empty.');
            }
            return $appName;
        });
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Do you need a ClearDb/mySql addon (set to free, ignite plan)?',
            // choices can also be PHP objects that implement __toString() method
            ['no', 'yes'],
            1
        );
        $this->infos->databaseNeeded = $helper->ask($input, $output, $question);
        $output->writeln('You have just selected: ' . $this->infos->databaseNeeded);
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
        $this->infos = NatInfos::getInstance();
        $this->infos->input = $input;
        $this->infos->output = $output;
        NatInfos::getInstance()->io =  new SymfonyStyle($input, $output);
        $this->infos->io = NatInfos::getInstance()->io;
        $this->processes = [];
        $this->message = Message::getInstance(); //call the unique Message instance (singleton)
        $this->infos->natProcess = new RunProcess();
    }
}
