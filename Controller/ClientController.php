<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends ContainerAware
{
    public function authenticationScriptBlockAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        $router = $this->container->get('router');

        if ($securityContext->isGranted('ROLE_USER')) {
            return new Response('');
        }

        $url = $router->generate('brouzie_crossdomain_auth_server_check_auth', array(
                'client' => $this->container->getParameter('brouzie.crossdomain_auth.authentication_server.client'),
            ), true);

        return new Response(sprintf('<script src="%s"></script>', $url));
    }
}
