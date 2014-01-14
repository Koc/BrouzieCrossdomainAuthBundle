<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\SecretKeyProvider;

interface SecretKeyProviderInterface
{
    /**
     * @param string $client
     *
     * @return string
     */
    public function getSecretKeyForClient($client);
}
