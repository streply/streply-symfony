<?php

namespace Streply\StreplyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Streply\Store\Providers\MemoryProvider;

final class StreplyExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('streply.streply_bundle.streply_client');
        $definition->setArgument(0, $config['dsn'] ?? '');
        $definition->setArgument(1, [
			'environment' => $container->getParameter('kernel.environment'),
			'storeProvider' => new MemoryProvider(),
		]);
    }
}
