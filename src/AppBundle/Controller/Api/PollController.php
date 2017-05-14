<?php

namespace AppBundle\Controller\Api;

use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class PollController
 * @package AppBundle\Controller\Api
 */
class PollController extends FOSRestController implements TokenAuthenticatedController
{
    /**
     * @View(serializerGroups={"Default"})
     * @return array
     */
    public function getPollsAction()
    {
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.repository_service.poll');

        return $pollRepository->getPolls([]);
    }

    /**
     * @View(serializerGroups={"Default", "Details"})
     * @param $id
     * @return null|object
     */
    public function getPollAction($id)
    {
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.repository_service.poll');
        $poll = $pollRepository->getPoll(['id' => $id]);
        if (!is_object($poll)) {
            throw $this->createNotFoundException();
        }
        return $poll;
    }

    public function getPollResultsAction($id)
    {
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.repository_service.poll');
        return $pollRepository->getResults($id);
    }
}
