<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Poll;
use AppBundle\Services\PollRepositoryService;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class PollController
 * @package AppBundle\Controller\Api
 */
class PollController extends FOSRestController
{
    /**
     * @Route("/api/polls", name="api_polls")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        /** @var PollRepositoryService $pollRepository */
        $pollRepository = $this->get('app.pollrepositoryservice');
        /** @var Poll[] $polls */
        $polls = $pollRepository->getPolls([]);
        $test = [];
        foreach ($polls as $poll) {
            $test[] = $poll->getTitle();
        }
        var_dump($test);
        return new JsonResponse($test);
    }
}
