<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle;

use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Brouzie\Bundle\CrossdomainAuthBundle\DependencyInjection\Security\Factory\CrossdomainAuthFactory;

class BrouzieCrossdomainAuthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $extension = $container->getExtension('security');
        /* @var $extension SecurityExtension */

        $extension->addSecurityListenerFactory(new CrossdomainAuthFactory());
    }
}
