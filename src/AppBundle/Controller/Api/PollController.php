<?php

namespace AppBundle\Controller\Api;

use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class PollController
 * @package AppBundle\Controller\Api
 */
class PollController extends FOSRestController
{
    public function getPollsAction()
    {
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.pollrepositoryservice');
        return $pollRepository->getPolls([]);
    }

    public function getPollAction($id)
    {
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.pollrepositoryservice');
        $poll = $pollRepository->getPoll(['id' => $id]);
        if(!is_object($poll)){
            throw $this->createNotFoundException();
        }
        return $poll;
    }

    public function getPollResultsAction($id)
    {

    }
}
