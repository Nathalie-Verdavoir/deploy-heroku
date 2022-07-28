<?php

namespace Nat\DeployBundle\Service;

use Nat\DeployBundle\NatDeployBundle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class CreateHtaccess
{
    public function __construct($input, $output)
    { 
        $bundle = NatDeployBundle::getInstance();
        $origin = $bundle->getDir().'/public/.htaccess';
        $to = $bundle->getDir().'/../../../public/.htaccess';
        $message = Message::getInstance($input, $output);
        $message->getColoredMessage('Creating .htaccess', 'blue');
        $filesystem= new Filesystem();
        try {
            $filesystem->copy($origin, $to);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your file at ".$exception->getPath();
        }
        $message->getColoredMessage('.htaccess done!', 'green');
    }
}
