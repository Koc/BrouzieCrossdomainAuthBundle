<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Controller;

use Brouzie\Bundle\CrossdomainAuthBundle\ResponseSigner\ResponseSignerInterface;
use Brouzie\Bundle\CrossdomainAuthBundle\Service\SecretKeyProvier;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends ContainerAware
{
    public function checkAuthenticationAction(Request $request)
    {
        $content = '/* not authenticated */';

        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('ROLE_USER')) {
            $client = $request->query->get('client');

            $secretKeyProvider = $this->container->get('metal.security.crossdomain_auth.secret_key_provider');
            /* @var $secretKeyProvider SecretKeyProvier */

            $responseSigner = $this->container->get('brouzie.crossdomain_auth.response_signer');
            /* @var $responseSigner ResponseSignerInterface */

            $secretKey = $secretKeyProvider->getSecretKeyForClient($client);
            $user = $securityContext->getToken()->getUser();
            $signature = $responseSigner->signUser($user, $secretKey);

            $authenticationToken = sprintf('%s*%s', $user->getUsername(), $signature);

            $content = <<<JS
var separator = document.location.href.indexOf('?') > -1 ? '&' : '?';
document.location.href = document.location.href + separator + '_authentication_token=$authenticationToken';
JS;
        }

//        $sessionData = $request->getSession()->all();
//        vd(unserialize($sessionData['_security_user_authentication']));

        return new Response($content, Response::HTTP_OK, array('Content-Type', 'text/javascript'));
    }
}
