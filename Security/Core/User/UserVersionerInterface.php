<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Security\Core\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserVersionerInterface
{
    public function incrementUserVersion(UserInterface $user);
}
