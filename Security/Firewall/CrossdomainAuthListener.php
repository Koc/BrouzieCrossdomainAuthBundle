<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Security\Firewall;

use Psr\Log\LoggerInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Http\SecurityEvents;

use Brouzie\Bundle\CrossdomainAuthBundle\Security\Authentication\Token\CrossdomainAuthToken;
use Brouzie\Bundle\CrossdomainAuthBundle\Security\Core\User\VersionableUserInterface;

class CrossdomainAuthListener implements ListenerInterface
{
    private $securityContext;
    private $authenticationManager;
    private $client;
    private $logger;
    private $dispatcher;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $client, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->client = $client;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
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

                if (null !== $this->logger) {
                    $this->logger->debug('SecurityContext erased because crossdomain-authentication token version'
                        .' does not corresponding to the user version.');
                }
            }
        }

        $request = $event->getRequest();
        $authenticationToken = $request->query->get('_authentication_token');

        if (!$authenticationToken) {
            return null;
        }

        $token = new CrossdomainAuthToken();
        $token->setAuthenticationToken($authenticationToken);
        $token->setClient($this->client);

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);

            if (null !== $this->dispatcher) {
                $loginEvent = new InteractiveLoginEvent($request, $token);
                $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
            }

            if (null !== $this->logger) {
                $this->logger->debug('SecurityContext populated with crossdomain-authentication token.');
            }

            $queryParams = $request->query->all();
            unset($queryParams['_authentication_token']);
            $suffix = count($queryParams) ? '?'.http_build_query($queryParams, null, '&') : '';
            $targetUrl = $request->getUriForPath($request->getPathInfo().$suffix);

            $event->setResponse(new RedirectResponse($targetUrl));
        } catch (AuthenticationException $failed) {
            $this->securityContext->setToken(null);

            if (null !== $this->logger) {
                $this->logger->warning(
                    'SecurityContext not populated with crossdomain-authentication token as the'
                    .' AuthenticationManager rejected the AuthenticationToken returned'
                    .' by the RememberMeServices: '.$failed->getMessage()
                );
            }
        }
    }
}
