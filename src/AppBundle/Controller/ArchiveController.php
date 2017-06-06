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
}
