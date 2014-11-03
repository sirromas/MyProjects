<?php

namespace Odesk\Bundle\PhystrixBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class OdeskPhystrixExtension configures phystrix services
 * @package Odesk\Bundle\PhystrixBundle\DependencyInjection
 */
class OdeskPhystrixExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('config.yml');

        $container->setParameter('phystrix.configuration.data', $config);
    }
}
