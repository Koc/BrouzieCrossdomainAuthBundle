<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class CrossdomainAuthFactory implements SecurityFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.brouzie_crossdomain_auth.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('brouzie.crossdomain_auth.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.brouzie_crossdomain_auth.'.$id;
        $container->setDefinition($listenerId, new DefinitionDecorator('brouzie.crossdomain_auth.security.authentication.listener'));

        $logoutListener = $container->getDefinition('security.logout_listener.'.$id);

        $userVersionerListenerId = 'security.logout.handler.target_path_fixer';
        $userVersionerListener = $container->setDefinition($userVersionerListenerId, new DefinitionDecorator('brouzie.crossdomain_auth.security.logout.handler.target_path_fixer'));
        $userVersionerListener->replaceArgument(1, $config['logout']);

        $logoutListener->addMethodCall('addHandler', array(new Reference($userVersionerListenerId)));

        if (isset($config['user_versioner'])) {
            $userVersionerListenerId = 'security.logout.handler.user_versioner';
            $userVersionerListener = $container->setDefinition($userVersionerListenerId, new DefinitionDecorator('brouzie.crossdomain_auth.security.logout.handler.user_versioner'));
            $userVersionerListener->replaceArgument(0, new Reference($config['user_versioner']));

            $logoutListener->addMethodCall('addHandler', array(new Reference($userVersionerListenerId)));
        }

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'brouzie_crossdomain_auth';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('user_versioner')->end()
                ->arrayNode('logout')
                    ->children()
                        ->scalarNode('target_path_parameter')->defaultValue('_redirect_to')->end()
                        ->booleanNode('use_referer')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
