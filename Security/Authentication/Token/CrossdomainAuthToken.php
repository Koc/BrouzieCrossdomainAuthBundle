<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class CrossdomainAuthToken extends AbstractToken
{
    private $authenticationToken;

    private $client;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * @param string $authenticationToken
     */
    public function setAuthenticationToken($authenticationToken)
    {
        $this->authenticationToken = $authenticationToken;
    }

    /**
     * @return string
     */
    public function getAuthenticationToken()
    {
        return $this->authenticationToken;
    }

    /**
     * @param string $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $username
     * @param string $signature
     *
     * @return string
     */
    public static function composeAuthenticationToken($username, $signature)
    {
        return sprintf('%s*%s', $username, $signature);
    }

    /**
     * @param string $authenticationToken
     *
     * @return array of username and signature
     */
    public static function decodeAuthenticationToken($authenticationToken)
    {
        $pos = strrpos($authenticationToken, '*');

        return array(substr($authenticationToken, 0, $pos), substr($authenticationToken, $pos + 1));
    }

    public function getCredentials()
    {
        return '';
    }
}
