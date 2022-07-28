<?php

namespace Nat\DeployBundle\DependencyInjection;

use Nat\DeployBundle\Service\Message;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader ;

class NatDeployExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );
        $loader->load('services.yml');
        Message::getInstance(new InputInterface, new OutputInterface) // get the unique isntance of message
            ->getColoredMessage('Thanks for using Nat/Deploy ;)');
    }
}
