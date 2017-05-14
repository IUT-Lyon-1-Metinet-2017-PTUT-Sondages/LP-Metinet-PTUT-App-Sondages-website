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
    /**
     * @var string
     */
    private $token;

    /**
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    private $request;

    /**
     * ApiAuthService constructor.
     * @param RequestStack $requestStack
     * @param string       $token
     */
    public function __construct(RequestStack $requestStack, $token)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->token = $token;
    }

    /**
     * @throws ApiAuthenticationFailedException
     */
    public function checkToken()
    {
        if ($this->request->get('token') !== $this->token) {
            throw new ApiAuthenticationFailedException();
        }
    }
}
