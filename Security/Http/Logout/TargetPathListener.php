<?php

namespace Brouzie\Bundle\CrossdomainAuthBundle\Security\Http\Logout;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class TargetPathListener implements LogoutHandlerInterface
{
    protected $httpUtils;
    protected $options;

    public function __construct(HttpUtils $httpUtils, array $options)
    {
        $this->httpUtils = $httpUtils;

        $this->options = array_merge(array(
            'target_path_parameter' => '_target_path',
            'use_referer'           => false,
        ), $options);
    }

    /**
     * Creates redirect after logout
     *
     * @param Request        $request
     * @param Response       $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        if ($response instanceof RedirectResponse && $targetUrl = $this->determineTargetUrl($request)) {
            $response->setTargetUrl($this->httpUtils->generateUri($request, $targetUrl));
        }
    }

    /**
     * Builds the target URL according to the defined options.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function determineTargetUrl(Request $request)
    {
        if ($targetUrl = $request->get($this->options['target_path_parameter'], null, true)) {
            return $targetUrl;
        }

        if ($this->options['use_referer'] && ($targetUrl = $request->headers->get('Referer'))) {
            return $targetUrl;
        }

        return null;
    }
}
