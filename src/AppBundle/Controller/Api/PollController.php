<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Poll;
use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class PollController
 * @package AppBundle\Controller\Api
 */
class PollController extends FOSRestController implements TokenAuthenticatedControllerInterface
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
     * Returns a Poll by its id.
     * @View(serializerGroups={"Default", "Details"})
     * @param int $id
     * @return Poll
     */
    public function getPollAction($id)
    {
        $pollRepository = $this->get('app.repository_service.poll');
        $poll = $pollRepository->getPoll(['id' => $id]);

        if (!is_object($poll)) {
            throw $this->createNotFoundException();
        }

        return $poll;
    }

    /**
     * Return Poll's results by it's id.
     * @param int $id
     * @return array
     */
    public function getPollResultsAction($id)
    {
        $pollRepository = $this->get('app.repository_service.poll');

        return $pollRepository->getResults($id);
    }
}
