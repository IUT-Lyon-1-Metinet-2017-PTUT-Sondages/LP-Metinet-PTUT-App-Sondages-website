<?php

namespace AppBundle\Controller\Api;

use AppBundle\Services\ApiAuthService;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class ApiController
 * @package AppBundle\Controller\Api
 */
class ApiController extends FOSRestController
{
    protected function checkApiAuthentication()
    {
        /** @var ApiAuthService $authService */
        $authService = $this->get('app.apiAuthService');
        $authService->checkToken();
    }
}