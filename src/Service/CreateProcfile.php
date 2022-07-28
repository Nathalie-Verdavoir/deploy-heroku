<?php

namespace Nat\DeployBundle\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class CreateProcfile
{
    public function __construct($input, $output)
    { 
        $message = Message::getInstance($input, $output);
        $filesystem= new Filesystem();
        $message->getColoredMessage('Creating Procfile', 'blue'); 
        try {
            $filesystem->dumpFile('Procfile', 'web: heroku-php-apache2 public/');
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while dumping your file at ".$exception->getPath();
        }
        $message->getColoredMessage('Procfile done!', 'green');
    }
}
