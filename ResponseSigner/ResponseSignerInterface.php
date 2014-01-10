<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\ResponseSigner;

use Symfony\Component\Security\Core\User\UserInterface;

interface ResponseSignerInterface
{
    public function signUser(UserInterface $user, $secretKey);
}
