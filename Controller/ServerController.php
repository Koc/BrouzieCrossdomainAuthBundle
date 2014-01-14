<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Controller;

use Brouzie\Bundle\CrossdomainAuthBundle\ResponseSigner\ResponseSignerInterface;
use Brouzie\Bundle\CrossdomainAuthBundle\SecretKeyProvider\SecretKeyProviderInterface;

use Brouzie\Bundle\CrossdomainAuthBundle\Security\Authentication\Token\CrossdomainAuthToken;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ServerController extends ContainerAware
{
    public function checkAuthenticationAction(Request $request)
    {
        $content = '/* not authenticated */';

        $securityContext = $this->container->get('security.context');

        if ($securityContext->isGranted('ROLE_USER')) {
            $client = $request->query->get('client');

            $secretKeyProvider = $this->container->get('metal.security.crossdomain_auth.secret_key_provider');
            /* @var $secretKeyProvider SecretKeyProviderInterface */

            $responseSigner = $this->container->get('brouzie.crossdomain_auth.response_signer');
            /* @var $responseSigner ResponseSignerInterface */

            $secretKey = $secretKeyProvider->getSecretKeyForClient($client);
            $user = $securityContext->getToken()->getUser();
            /* @var $user UserInterface */
            $signature = $responseSigner->signUser($user, $secretKey);

            $username = $user->getUsername();
            $authenticationToken = CrossdomainAuthToken::composeAuthenticationToken($username, $signature);
            $authenticationToken = urlencode($authenticationToken);

            $content = <<<JS
/* authenticated as $username */
var separator = document.location.href.indexOf('?') > -1 ? '&' : '?';
document.location.href = document.location.href + separator + '_authentication_token=$authenticationToken';
JS;
        }

        return new Response($content, Response::HTTP_OK, array('Content-Type', 'text/javascript'));
    }
}
