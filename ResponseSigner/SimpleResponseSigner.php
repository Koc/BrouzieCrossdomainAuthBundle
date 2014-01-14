<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\ResponseSigner;

use Brouzie\Bundle\CrossdomainAuthBundle\Security\Core\User\VersionableUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SimpleResponseSigner implements ResponseSignerInterface
{
    public function signUser(UserInterface $user, $secretKey)
    {
        $data = array($user->getUsername(), $secretKey);
        if ($user instanceof VersionableUserInterface) {
            $data[] = $user->getUserVersion();
        }

        return sha1(implode(',', $data));
    }
}
