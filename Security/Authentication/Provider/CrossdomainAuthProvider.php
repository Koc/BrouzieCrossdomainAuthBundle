<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Security\Authentication\Provider;

use Brouzie\Bundle\CrossdomainAuthBundle\SecretKeyProvider\SecretKeyProviderInterface;
use Brouzie\Bundle\CrossdomainAuthBundle\Security\Core\User\VersionableUserInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use Brouzie\Bundle\CrossdomainAuthBundle\ResponseSigner\ResponseSignerInterface;
use Brouzie\Bundle\CrossdomainAuthBundle\Security\Authentication\Token\CrossdomainAuthToken;

class CrossdomainAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var ResponseSignerInterface
     */
    private $responseSigner;

    /**
     * @var SecretKeyProviderInterface
     */
    private $secretKeyProvider;

    public function __construct(UserProviderInterface $userProvider, ResponseSignerInterface $responseSigner, SecretKeyProviderInterface $secretKeyProvider)
    {
        $this->userProvider = $userProvider;
        $this->responseSigner = $responseSigner;
        $this->secretKeyProvider = $secretKeyProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return;
        }

        /* @var $token CrossdomainAuthToken */

        list ($username, $clientSignature) = CrossdomainAuthToken::decodeAuthenticationToken($token->getAuthenticationToken());

        $user = $this->userProvider->loadUserByUsername($username);
        $signature = $this->responseSigner->signUser($user, $this->secretKeyProvider->getSecretKeyForClient($token->getClient()));

        if ($signature === $clientSignature) {
            $authenticatedToken = new CrossdomainAuthToken($user->getRoles());
            $authenticatedToken->setUser($user);
            if ($user instanceof VersionableUserInterface) {
                $authenticatedToken->setUserVersion($user->getUserVersion());
            }
            $authenticatedToken->setAuthenticationToken($token->getAuthenticationToken());
            $authenticatedToken->setClient($token->getClient());

            return $authenticatedToken;
        }
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof CrossdomainAuthToken;
    }
}
