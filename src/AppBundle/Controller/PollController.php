<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Poll;
use AppBundle\Entity\User;
use AppBundle\Exception\InvalidVariantException;
use AppBundle\Exception\ValidationFailedException;
use AppBundle\Services\PollRepositoryService;
use AppBundle\Services\PollResultsService;
use AppBundle\Services\ValidationService;
use Doctrine\ORM\ORMInvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class PollController
 * @package AppBundle\Controller
 */
class PollController extends Controller
{
    /**
     * Display all user's polls, or all polls if current user is admin.
     * @Route("/backoffice/polls", name="backoffice_polls")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');
        $service   = $this->get('app.repository_service.poll');
        $user      = $this->getUser();

        if ($user->hasRole('ROLE_ADMIN')) {
            $entries = $service->getPolls([]);
        } else {
            $entries = $service->getPolls(['p.user' => $user]);
        }

        $pagination = $paginator->paginate(
            $entries,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('@App/backoffice/poll/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Add a new Poll.
     * @Route("/backoffice/polls/add", name="backoffice_polls_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        /** @var ValidationService $validationService */
        $validationService = $this->get('app.validation_service');

        if ($request->getMethod() == 'POST') {
            try {
                $validationService->validateAndCreatePollFromRequest($request, $this->getUser());
            } catch (ValidationFailedException $e) {
                return new JsonResponse($e->getErrors());
            } catch (InvalidVariantException $e) {
                return new JsonResponse([
                    'message'  => $e->getMessage(),
                    'question' => $e->getQuestion(),
                ]);
            }

            return $this->redirect($this->generateUrl('backoffice_polls'));
        }

        return $this->render('@App/backoffice/poll/add.html.twig');
    }

    /**
     * Display and handle the Poll edition form.
     * @Route("/backoffice/polls/{id}/edit", name="backoffice_poll_edit")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $pollRepositoryService = $this->get('app.repository_service.poll');
        list($jsonPoll, $poll) = $pollRepositoryService->getJsonPoll($id);
        /** @var string $jsonPoll */
        /** @var Poll $poll */

        if ($poll->isPublished()) {
            $this->addFlash('danger', "Il n'est pas possible de modifier un sondage publié.");

            return $this->redirectToRoute('backoffice_polls');
        }

        $validationService = $this->get('app.validation_service');
        $deletionService   = $this->get('app.deletion_service');

        if ($request->getMethod() == 'POST') {
            try {
                $deletionService->handleEntityDeletion($request->get('toDelete'));
                $validationService->validateAndCreatePollFromRequest($request, $this->getUser());
            } catch (ValidationFailedException $e) {
                return new JsonResponse($e->getErrors());
            } catch (InvalidVariantException $e) {
                return new JsonResponse([
                    'message'  => $e->getMessage(),
                    'question' => $e->getQuestion(),
                ]);
            }

            return $this->redirect($this->generateUrl('backoffice_polls'));
        }

        return $this->render('@App/backoffice/poll/edit.html.twig', ['poll' => $jsonPoll]);
    }

    /**
     * Delete a Poll by its id.
     * @Route("/backoffice/polls/{id}/delete", name="backoffice_poll_delete")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $service = $this->get('app.repository_service.poll');

        try {
            $service->deleteById($id);
        } catch (ORMInvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('backoffice_polls');
        }

        return $this->redirectToRoute('backoffice_polls');
    }

    /**
     * Send a mail
     * @Route("/backoffice/send-mail", name="backoffice_send_mail")
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function sendMailAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $mail        = $request->get('email');
            $id          = $request->get('id');
            $mailService = $this->get('app.mailer_service');
            $userMail    = $this->getUser()->getEmail();
            $response    = $mailService->sharePoll($userMail, $mail, $id);
            $response    = new JsonResponse($response);
            return $response;
        } else {
            $response = new JsonResponse(false);
            return $response;
        }
    }

    /**
     * Publish a Poll.
     * @Route("/backoffice/polls/{id}/publish", name="backoffice_poll_publish")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishAction($id)
    {
        $pollRepositoryService = $this->get('app.repository_service.poll');
        /** @var Poll $poll */
        $poll = $pollRepositoryService->getPoll(['id' => $id]);

        if (!is_null($poll)) {
            $poll->publish();
            $pollRepositoryService->save($poll);
            $this->addFlash('success', "Le sondage a bien été publié.");
        } else {
            $this->addFlash('danger', "Aucun sondage ne correspond à cet identifiant.");
        }

        return $this->redirectToRoute('backoffice_polls');
    }

    /**
 * Display a Poll's results by its id.
 * @Route("/backoffice/polls/{id}/results", name="backoffice_poll_results")
 * @param int $id
 * @return \Symfony\Component\HttpFoundation\Response
 */
    public function resultsAction($id)
    {
        /** @var PollRepositoryService $pollService */
        $pollService       = $this->get('app.repository_service.poll');
        /** @var PollResultsService $pollResultService */
        $pollResultsService = $this->get('app.poll.results');
        $poll              = $pollService->getPoll(['id' => $id]);
        $charts            = $pollResultsService->getChartsResults($pollService->getResults($id));

        return $this->render('@App/backoffice/poll/results.html.twig', [
            'poll'   => $poll,
            'charts' => $charts,
        ]);
    }

    /**
     * Export a Poll's results by its id as Excel.
     * @Route("/backoffice/polls/{id}/export", name="backoffice_poll_export")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportExcelAction($id)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var PollRepositoryService $pollService */
        $pollService = $this->get('app.repository_service.poll');
        $poll        = $pollService->getPoll(['id' => $id]);

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
