<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('brouzie_crossdomain_auth');

        $rootNode
            ->children()
                ->arrayNode('authentication_server')
                    ->children()
                        ->scalarNode('url')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('client')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('secret_key')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->scalarNode('secret_key_provider')->end()
                ->scalarNode('response_signer')
                    ->cannotBeEmpty()
                    ->defaultValue('brouzie.crossdomain_auth.simple_response_signer')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
