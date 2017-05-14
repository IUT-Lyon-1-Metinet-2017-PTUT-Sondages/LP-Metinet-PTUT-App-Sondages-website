<?php

namespace AppBundle\Controller\Api;

use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class UserController
 * @package AppBundle\Controller\Api
 */
class UserController extends FOSRestController implements TokenAuthenticatedControllerInterface
{

    /**
     * Return User's polls by it's id.
     * @View(serializerGroups={"Default", "Details"})
     * @param int $id
     * @return array
     */
    public function getUserPollsAction($id)
    {
        $pollRepository = $this->get('app.repository_service.poll');

        return $pollRepository->getPolls(['user' => $id]);
    }
}
