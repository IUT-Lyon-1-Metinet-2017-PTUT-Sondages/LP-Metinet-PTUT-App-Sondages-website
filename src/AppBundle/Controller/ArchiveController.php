<?php

namespace AppBundle\Controller;

use AppBundle\Form\PollViewType;
use AppBundle\Services\PollRepositoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArchiveController extends Controller
{
    /**
     * Display and handle the Archived Polls.
     * @Route("/backoffice/polls/archive", name="backoffice_polls_archive")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function archiveListAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');
        $service   = $this->get('app.repository_service.poll');
        $user      = $this->getUser();

        if ($user->hasRole('ROLE_ADMIN')) {
            $entries = $service->getArchivedPolls([]);
        } else {
            $entries = $service->getArchivedPolls(['p.user' => $user]);
        }

        $pagination = $paginator->paginate(
            $entries,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('@App/backoffice/poll/archive.html.twig', [
            'pagination' => $pagination
        ]);
    }
    /**
     * Delete a Poll by its id.
     * @Route("/backoffice/polls/archive/{id}/delete", name="backoffice_poll_archive_delete")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $service = $this->get('app.repository_service.poll');

        try {
            $service->hardDeleteById($id);
        } catch (ORMInvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('backoffice_polls_archive');
        }

        return $this->redirectToRoute('backoffice_polls_archive');
    }

    /**
     * Display and handle the Poll form.
     * @Route("/a/archive/{token}", name="answer_poll_archive")
     * @param Request $request
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function answerPollAction(Request $request, $token)
    {

        $service = $this->get('app.repository_service.poll');
        $translator = $this->get('translator');

        $poll = $service->getArchivedPoll(['p.accessToken' => $token]);

        if (is_null($poll)) {
            throw $this->createNotFoundException($translator->trans('front.poll.error.not_found', [], 'AppBundle'));
        }

        $currentUserHasCreatedThisPoll = $poll->getUser() === $this->getUser() || $this->isGranted('ROLE_ADMIN');

        if (!$poll->isPublished()) {
            if ($currentUserHasCreatedThisPoll) {
                $this->addFlash('warning', $translator->trans('poll.message_preview_for_author', [], 'AppBundle'));
            } else {
                $this->addFlash('danger', $translator->trans('poll.error_message_preview_for_not_author', [], 'AppBundle'));

                return $this->redirectToRoute('homepage');
            }
        }

        $form = $this->createForm(PollViewType::class, [], [
            'poll' => $poll,
            'currentUserHasCreatedThisPoll' => $currentUserHasCreatedThisPoll,
        ]);


        return $this->render('@App/polls/answer.html.twig', [
            'pollView'
             => $form->createView(),
            'poll' => $poll,
        ]);
    }
}
