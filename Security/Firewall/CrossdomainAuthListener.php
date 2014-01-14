<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Security\Firewall;

use Brouzie\Bundle\CrossdomainAuthBundle\Security\Authentication\Token\CrossdomainAuthToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

class CrossdomainAuthListener extends AbstractAuthenticationListener
{
    private $client;

    /**
     * @param string $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $authenticationToken = $request->query->get('_authentication_token');

        if (!$authenticationToken) {
            return null;
        }

        $token = new CrossdomainAuthToken();
        $token->setAuthenticationToken($authenticationToken);
        $token->setClient($this->client);

        return $this->authenticationManager->authenticate($token);
    }
}
