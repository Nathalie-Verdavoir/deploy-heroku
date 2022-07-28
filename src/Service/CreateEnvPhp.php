<?php

namespace Nat\DeployBundle\Service;

use Nat\DeployBundle\NatDeployBundle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class CreateEnvPhp
{
    public function __construct($input, $output, $io)
    { 
        $this->message->getColoredMessage('Creating .env.php file', 'blue');
        $processes = [
            ['composer', 'dump-env', 'prod'],
        ];
        RunProcess::getInstance($input, $output, $io)->runProcesses($processes);
        $filesystem= new Filesystem();
        try {
            $filesystem->copy('.env.local.php', '.env.php');
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your file at ".$exception->getPath();
        }

        try {
            $filesystem->dumpFile('.env.php', "<?php

        return array (
          'APP_ENV' => 'prod',
        );
        ");
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while dumping your file at ".$exception->getPath();
        }
    }
}
