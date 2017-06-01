<?php

namespace AppBundle\Services;
use AppBundle\Exception\ApiAuthenticationFailedException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ApiAuthService
 * @package AppBundle\Services
 */
class ApiAuthService
{
    private $token;
    private $request;

    public function __construct(RequestStack $requestStack, $token)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->token   = $token;
    }

    public function checkToken()
    {
        if($this->request->get('token') != $this->token) {
            throw new ApiAuthenticationFailedException();
        }
    }
}