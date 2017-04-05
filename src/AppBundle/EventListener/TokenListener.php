<?php

namespace AppBundle\EventListener;

use AppBundle\Controller\Api\TokenAuthenticatedController;
use AppBundle\Exception\ApiAuthenticationFailedException;
use AppBundle\Services\ApiAuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class TokenListener
 */
class TokenListener
{
    private $apiAuthService;

    public function __construct(ApiAuthService $apiAuthService)
    {
        $this->apiAuthService = $apiAuthService;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof TokenAuthenticatedController) {
            $this->apiAuthService->checkToken();
        }
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof ApiAuthenticationFailedException) {
            return;
        }

        $response = new JsonResponse(
            ['error' => ['code' => $exception->getCode(), 'message' => $exception->getMessage()]],
            $exception->getCode()
        );

        $event->setResponse($response);
    }
}