<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Service;

interface SecretKeyProvier
{
    public function getSecretKeyForClient($client);
}
