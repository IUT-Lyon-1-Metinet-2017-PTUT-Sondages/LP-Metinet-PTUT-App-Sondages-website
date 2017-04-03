<?php

namespace AppBundle\Services;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class ApiAuthService
 * @package AppBundle\Services
 */
class ApiAuthService
{
    private $token;
    private $request;

    public function __construct(RequestStack $requestStack, string $token)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->token   = $token;
    }

    public function checkToken()
    {
        if($this->request->get('token') != $this->token) {
            throw new AuthenticationException();
        }
    }
}