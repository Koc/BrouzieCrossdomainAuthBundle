<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\SecretKeyProvider;

class ArraySecretKeyProvider implements SecretKeyProviderInterface
{
    protected $keys;

    public function __construct(array $keys = array())
    {
        $this->keys = $keys;
    }

    public function getSecretKeyForClient($client)
    {
        return $this->keys[$client];
    }
}
