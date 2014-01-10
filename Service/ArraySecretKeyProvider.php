<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Service;

class ArraySecretKeyProvider implements SecretKeyProvier
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
