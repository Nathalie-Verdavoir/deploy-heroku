<?php

namespace Nat\DeployBundle\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class ChangeEnvLocalPhpToDev
{
    public function __construct()
    {
        $message = Message::getInstance();
        $message->getColoredMessage('Change .env.local.php to dev', 'blue');
       
        $filesystem = new Filesystem();
        try {
            $devlocalphp = str_replace('prod','dev',file_get_contents('.env.local.php'));
            $filesystem->dumpFile('.env.local.php', $devlocalphp);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while changing app_env to dev your file at " . $exception->getPath();
        }
        NatInfos::getInstance()->io->progressAdvance(10);
        $message->getColoredMessage('.env.php done!', 'green');
    }
}
