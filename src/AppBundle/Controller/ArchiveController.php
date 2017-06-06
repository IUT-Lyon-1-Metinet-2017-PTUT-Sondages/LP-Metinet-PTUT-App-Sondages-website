<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PollViewType;
use AppBundle\Services\PollRepositoryService;
use AppBundle\Services\PollResultsService;
use Doctrine\ORM\ORMInvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function answerPollAction($token)
    {
        /** @var PollRepositoryService $service */
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

    /**
     * Display a Poll's results by its id.
     * @Route("/backoffice/polls/archive/{id}/results", name="backoffice_poll_archive_results")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resultsAction($id)
    {
        /** @var PollRepositoryService $pollService */
        $pollService       = $this->get('app.repository_service.poll');
        /** @var PollResultsService $pollResultService */
        $pollResultsService = $this->get('app.poll.results');
        $poll              = $pollService->getArchivedPoll(['p.id' => $id]);
        $charts            = $pollResultsService->getChartsResults($pollService->getResults($id));

        return $this->render('@App/backoffice/poll/results.html.twig', [
            'poll'   => $poll,
            'charts' => $charts,
        ]);
    }

    /**
     * Export a Poll's results by its id as Excel.
     * @Route("/backoffice/polls/archive/{id}/export", name="backoffice_poll_archive_export")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportExcelAction($id)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var PollRepositoryService $pollService */
        $pollService = $this->get('app.repository_service.poll');
        $poll        = $pollService->getArchivedPoll(['p.id' => $id]);

        /** @var PollResultsService $pollResultsService */
        $pollResultsService = $this->get('app.poll.results');
        $response           = $pollResultsService->getExcelResults($poll, $user, $pollService->getResults($id));

        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $poll->getTitle()) . '.xlsx'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
