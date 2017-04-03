<?php

namespace AppBundle\Controller\Api;

use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class UserController
 * @package AppBundle\Controller\Api
 */
class UserController extends FOSRestController
{
    public function getUserPollsAction($id)
    {
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.pollrepositoryservice');
        $polls = $pollRepository->getPolls(['user_id' => $id]);
        return $polls;
    }
}
