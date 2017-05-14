<?php

namespace AppBundle\Controller\Api;

use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class UserController
 * @package AppBundle\Controller\Api
 */
class UserController extends FOSRestController implements TokenAuthenticatedController
{

    /**
     * @View(serializerGroups={"Default", "Details"})
     * @param $id
     * @return array
     */
    public function getUserPollsAction($id)
    {
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.repository_service.poll');
        $polls = $pollRepository->getPolls(['user' => $id]);
        return $polls;
    }
}
