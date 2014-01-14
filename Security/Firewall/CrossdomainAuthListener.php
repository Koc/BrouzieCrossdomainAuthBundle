<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

use Brouzie\Bundle\CrossdomainAuthBundle\Security\Authentication\Token\CrossdomainAuthToken;
use Brouzie\Bundle\CrossdomainAuthBundle\Security\Core\User\VersionableUserInterface;

class CrossdomainAuthListener implements ListenerInterface
{
    private $securityContext;
    private $authenticationManager;
    private $client;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $client)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $token = $this->securityContext->getToken();
        if ($token instanceof CrossdomainAuthToken) {
            $user = $token->getUser();
            if ($user && $user instanceof VersionableUserInterface && $user->getUserVersion() != $token->getUserVersion()) {
                // user logged out
                $this->securityContext->setToken(null);
            }
        }

        $request = $event->getRequest();
        $authenticationToken = $request->query->get('_authentication_token');

        if (!$authenticationToken) {
            return null;
        }
        //TODO: add logout handlers for updation user version

        $token = new CrossdomainAuthToken();
        $token->setAuthenticationToken($authenticationToken);
        $token->setClient($this->client);

        try {
            $authToken = $this->authenticationManager->authenticate($token);

            $this->securityContext->setToken($authToken);
            //TODO: add logging
            //TODO: dispatch SecurityEvents::INTERACTIVE_LOGIN event

            $queryParams = $request->query->all();
            unset($queryParams['_authentication_token']);
            $suffix = count($queryParams) ? '?'.http_build_query($queryParams, null, '&') : '';
            $targetUrl = $request->getUriForPath($request->getPathInfo().$suffix);

            $event->setResponse(new RedirectResponse($targetUrl));
        } catch (AuthenticationException $failed) {
            $this->securityContext->setToken(null);
        }
    }
}
