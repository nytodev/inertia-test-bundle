<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class InertiaSymfonyBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('root_view')
                    ->defaultValue('base.html.twig')
                    ->info('The root view that Inertia.js will render.')
                ->end()
                ->arrayNode('ssr')
                    ->canBeDisabled()
                    ->children()
                        ->scalarNode('url')
                            ->defaultValue('http://localhost:13714/render')
                            ->info('The URL of the Inertia.js server-side rendering server.')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $builder->setParameter('inertia_symfony.root_view', $config['root_view']);

        if (isset($config['ssr']) && $config['ssr']['enabled']) {
            $builder->setParameter('inertia_symfony.ssr.url', $config['ssr']['url']);
        }
    }
}
