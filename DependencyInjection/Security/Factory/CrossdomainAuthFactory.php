<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class CrossdomainAuthFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('check_path', '/crossdomain_authentication');
    }

    /**
     * {@inheritDoc}
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'security.authentication.provider.brouzie_crossdomain_auth.'.$id;

        $container->setParameter('brouzie.crossdomain_auth.check_path', $config['check_path']);

        $container
            ->setDefinition($providerId, new DefinitionDecorator('brouzie.crossdomain_auth.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
        ;

        return $providerId;
    }

    /**
     * {@inheritDoc}
     */
    protected function getListenerId()
    {
        return 'brouzie.crossdomain_auth.security.authentication.listener';
    }

    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'brouzie_crossdomain_auth';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        return;

        $builder = $node->children();
        $builder
            ->scalarNode('auth_server')->cannotBeEmpty()->isRequired()->end()
            //->scalarNode('check_path')->defaultValue('/authenticate')->cannotBeEmpty()->isRequired()->end()
        ;
    }
}
