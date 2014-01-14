<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\ResponseSigner;

use Symfony\Component\Security\Core\User\UserInterface;

interface ResponseSignerInterface
{
    /**
     * @param UserInterface $user
     * @param $secretKey
     *
     * @return mixed
     */
    public function signUser(UserInterface $user, $secretKey);
}
