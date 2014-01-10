<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\ResponseSigner;

use Symfony\Component\Security\Core\User\UserInterface;

class SimpleResponseSigner implements ResponseSignerInterface
{
    public function signUser(UserInterface $user, $secretKey)
    {
        return sha1(implode(',', array($user->getUsername(), $secretKey)));
    }
}
