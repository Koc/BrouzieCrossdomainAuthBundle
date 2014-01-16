<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Security\Http\Logout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

use Brouzie\Bundle\CrossdomainAuthBundle\Security\Core\User\UserVersionerInterface;

class UserVersionerLogoutListener implements LogoutHandlerInterface
{
    private $userVersioner;

    public function __construct(UserVersionerInterface $userVersioner)
    {
        $this->userVersioner = $userVersioner;
    }

    /**
     * Increment user version
     *
     * @param Request        $request
     * @param Response       $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->userVersioner->incrementUserVersion($token->getUser());
    }
}
