<?php

namespace AppBundle\Controller;

use AppBundle\Form\PollViewType;
use AppBundle\Services\PollRepositoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    /**
     * Display and handle the Poll form.
     * @Route("/a/{token}", name="answer_poll")
     * @param Request $request
     * @param string  $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function answerPollAction(Request $request, $token)
    {
        /** @var PollRepositoryService $service */
        $service = $this->get('app.repository_service.poll');
        $poll = $service->getPoll(['accessToken' => $token]);

        if (is_null($poll)) {
            throw $this->createNotFoundException('Le sondage que vous recherchez n\'existe pas');
        }

        $currentUserHasCreatedThisPoll = $poll->getUser() === $this->getUser() || $this->isGranted('ROLE_ADMIN');

        if (!$poll->isPublished()) {
            if ($currentUserHasCreatedThisPoll) {
                $this->addFlash(
                    'warning',
                    "Ceci est un aperçu du rendu final du sondage, toute interaction a été désactivée.<br>"
                    . " Le sondage n'est pour l'instant uniquement accessible que par vous-même."
                );
            } else {
                $this->addFlash('danger', "Vous n'avez pas encore accès à ce sondage.");

                return $this->redirectToRoute('homepage');
            }
        }

        $form = $this->createForm(PollViewType::class, [], [
            'poll' => $poll,
            'currentUserHasCreatedThisPoll' => $currentUserHasCreatedThisPoll,
        ]);

        $form->handleRequest($request);

        if ($poll->isPublished() && $form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $pollAnswersService = $this->get('app.poll.answers');
                $pollAnswersService->registerAnswers($poll, $data);

                return $this->render('@App/polls/thanks.html.twig', [
                    'poll' => $poll,
                ]);
            }
        }

        return $this->render('@App/polls/answer.html.twig', [
            'pollView' => $form->createView(),
            'poll' => $poll,
        ]);
    }
}
