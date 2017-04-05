<?php

namespace AppBundle\Controller\Api;

use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * Class PollController
 * @package AppBundle\Controller\Api
 */
class PollController extends ApiController
{
    /**
     * @View(serializerGroups={"Default"})
     * @return array
     */
    public function getPollsAction()
    {
        $this->checkApiAuthentication();
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.pollrepositoryservice');

        return $pollRepository->getPolls([]);
    }

    /**
     * @View(serializerGroups={"Default", "Details"})
     * @param $id
     * @return null|object
     */
    public function getPollAction($id)
    {
        $this->checkApiAuthentication();
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
        $this->checkApiAuthentication();
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.pollrepositoryservice');
        return $pollRepository->getResults($id);
    }
}
