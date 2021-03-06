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

        // add check to the base path in future
        if ($request->getHost() === $this->container->getParameter('brouzie.crossdomain_auth.authentication_server.host')) {
            return new Response('');
        }

        $url = $router->generate('brouzie_crossdomain_auth_server_check_auth', array(
                'client' => $this->container->getParameter('brouzie.crossdomain_auth.authentication_server.client'),
            ), true);

        return new Response(sprintf('<script type="text/javascript" src="%s"></script>', $url));
    }
}
