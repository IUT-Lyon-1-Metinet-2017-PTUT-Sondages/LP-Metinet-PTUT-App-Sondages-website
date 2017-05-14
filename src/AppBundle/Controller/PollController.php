<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Poll;
use AppBundle\Exception\InvalidVariantException;
use AppBundle\Exception\ValidationFailedException;
use AppBundle\Services\ValidationService;
use Avegao\ChartjsBundle\Chart\PieChart;
use Avegao\ChartjsBundle\DataSet\PieDataSet;
use Doctrine\ORM\ORMInvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PollController
 * @package AppBundle\Controller
 */
class PollController extends Controller
{
    /**
     * Display all user's polls, or all polls if current user is admin.
     * @Route("/backoffice/polls", name="backoffice_polls")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $service = $this->get('app.repository_service.poll');
        $user = $this->getUser();

        if ($user->hasRole('ROLE_ADMIN')) {
            $polls = $service->getPolls([]);
        } else {
            $polls = $service->getPolls(['user' => $user]);
        }

        return $this->render('@App/backoffice/poll/index.html.twig', ['polls' => $polls]);
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
                    'message' => $e->getMessage(),
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
     * @param int     $id
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
        $deletionService = $this->get('app.deletion_service');

        if ($request->getMethod() == 'POST') {
            try {
                $deletionService->handleEntityDeletion($request->get('toDelete'));
                $validationService->validateAndCreatePollFromRequest($request, $this->getUser());
            } catch (ValidationFailedException $e) {
                return new JsonResponse($e->getErrors());
            } catch (InvalidVariantException $e) {
                return new JsonResponse([
                    'message' => $e->getMessage(),
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
            $service->deleteById(['id' => $id]);
        } catch (ORMInvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('backoffice_polls');
        }

        return $this->redirectToRoute('backoffice_polls');
    }

    /**
     * Public a Poll.
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
        $service = $this->get('app.repository_service.poll');
        $poll = $service->getPoll(['id' => $id]);

        $charts = [];
        $questionsAnswers = $service->getResults($id);
        $questionsAnswersAfterTreatment = [];
        $checkId = -1;

        foreach ($questionsAnswers as $questionAnswers) {
            if ($checkId === $questionAnswers['qId']) {
                continue;
            }

            $checkId = $questionAnswers['qId'];

            $props = array_filter(
                $questionsAnswers,
                function ($a) use ($checkId) {
                    return $a['qId'] === $checkId;
                }
            );

            unset($questionAnswers['propId']);
            unset($questionAnswers['propTitle']);
            unset($questionAnswers['amount']);

            foreach ($props as $j => $prop) {
                $questionAnswers['props'][] = [
                    'id' => $prop['propId'],
                    'title' => $prop['propTitle'],
                    'amount' => $prop['amount'],
                ];
            }

            $questionsAnswersAfterTreatment[] = $questionAnswers;
        }

        foreach ($questionsAnswersAfterTreatment as $question) {
            $chart = new PieChart();
            $dataSet = new PieDataSet();
            $data = [];
            $labels = [];
            foreach ($question['props'] as $index => $proposition) {
                $labels[] = $proposition['title'];
                $data[] = $proposition['amount'];
                $dataSet->addBackgroundColor($this->getParameter('graph_colors')[$index]);
            }
            $dataSet->setData($data);
            $chart->addDataSet($dataSet);
            $chart->setLabels($labels);
            $chart->generateData();
            $charts[] = ['question' => $question, 'chart' => $chart];
        }

        return $this->render('@App/backoffice/poll/results.html.twig', [
            'poll' => $poll,
            'charts' => $charts,
        ]);
    }
}
