<?php

namespace Nat\DeployBundle;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NatDeployBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__;
    }

    private static $containerInstance = null;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        self::$containerInstance = $container;
    }

    public static function getContainer()
    {
        return self::$containerInstance;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // load an XML, PHP or Yaml file
        $container->import('../config/services.yml');

    }
}