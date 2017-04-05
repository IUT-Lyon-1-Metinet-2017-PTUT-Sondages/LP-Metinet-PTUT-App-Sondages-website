<?php

namespace AppBundle\Controller\Api;

use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * Class UserController
 * @package AppBundle\Controller\Api
 */
class UserController extends ApiController
{

    /**
     * @View(serializerGroups={"Default", "Details"})
     * @param $id
     * @return array
     */
    public function getUserPollsAction($id)
    {
        $this->checkApiAuthentication();
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.pollrepositoryservice');
        $polls = $pollRepository->getPolls(['user_id' => $id]);
        return $polls;
    }
}
