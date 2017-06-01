<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Poll;
use AppBundle\Form\PollViewType;
use AppBundle\Services\PollAnswersService;
use AppBundle\Services\PollRepositoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    /**
     * @Route("/{token}", name="answer_poll")
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function answerPollAction(Request $request, $token)
    {
        /** @var PollRepositoryService $service */
        $service = $this->get('app.pollRepositoryService');
        $poll = $service->getPoll(['accessToken' => $token]);
        if ($poll instanceof Poll) {
            $form = $this->createForm(PollViewType::class, [], ['poll' => $poll]);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $data = $form->getData();
                    /** @var PollAnswersService $pollAnswersService */
                    $pollAnswersService = $this->get('app.poll.answers');
                    $pollAnswersService->registerAnswers($poll, $data);
                    return $this->render('@App/polls/thanks.html.twig', [
                        'poll' => $poll
                    ]);
                }
            }
            return $this->render('@App/polls/answer.html.twig', [
                'pollView' => $form->createView(),
                'poll'     => $poll
            ]);
        }

        throw $this->createNotFoundException('Le sondage que vous recherchez n\'existe pas');
    }
}
