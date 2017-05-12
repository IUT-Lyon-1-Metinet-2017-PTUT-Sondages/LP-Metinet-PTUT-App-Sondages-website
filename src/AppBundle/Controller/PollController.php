<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Services\PollRepositoryService;
use AppBundle\Exception\InvalidVariantException;
use AppBundle\Exception\ValidationFailedException;
use AppBundle\Services\ValidationService;
use Avegao\ChartjsBundle\Chart\PieChart;
use Avegao\ChartjsBundle\DataSet\PieDataSet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class PollController
 * @package AppBundle\Controller
 */
class PollController extends Controller
{
    /**
     * @Route("/backoffice/polls", name="backoffice_polls")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $service = $this->get('app.pollRepositoryService');
        $user = $this->get('security.token_storage')
                     ->getToken()
                     ->getUser();

        if ($user->hasRole('ROLE_ADMIN')) {
            $polls = $service->getPolls([]);
        } else {
            $polls = $service->getPolls(['user' => $user]);
        }

        // replace this example code with whatever you need
        return $this->render(
            '@App/backoffice/poll/index.html.twig',
            [
                'polls' => $polls,
            ]
        );
    }

    /**
     * @Route("/backoffice/polls/add", name="backoffice_polls_add")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        /** @var ValidationService $validationService */
        $validationService = $this->get('app.validationService');
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($request->getMethod() == 'POST') {
            try {
                $validationService->validateAndCreatePollFromRequest($request, $user);
                return $this->redirect($this->generateUrl('backoffice_polls'));
            } catch (ValidationFailedException $e) {
                return new JsonResponse($e->getErrors());
            } catch (InvalidVariantException $e) {
                return new JsonResponse([
                    'message' => $e->getMessage(),
                    'question' => $e->getQuestion(),
                ]);
            }
        }

        return $this->render('@App/backoffice/poll/add.html.twig');
    }

    /**
     * @Route("/backoffice/polls/{id}/edit", name="backoffice_poll_edit")
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $service = $this->get('app.pollRepositoryService');
        $poll = $service->getJsonPoll($id);

        $validationService = $this->get('app.validationService');
        $user = $this->get('security.token_storage')
                     ->getToken()
                     ->getUser();
        if ($request->getMethod() == 'POST') {
            try {
                $validationService->validateAndCreatePollFromRequest($request, $user);
                return $this->redirect($this->generateUrl('backoffice_polls'));
            } catch (ValidationFailedException $e) {
                return new JsonResponse($e->getErrors());
            } catch (InvalidVariantException $e) {
                return new JsonResponse([
                    'message' => $e->getMessage(),
                    'question' => $e->getQuestion(),
                ]);
            }
        }

        return $this->render(
            '@App/backoffice/poll/edit.html.twig',
            [
                'poll' => $poll,
            ]
        );
    }

    /**
     * @Route("/backoffice/polls/{id}/delete", name="backoffice_poll_delete")
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $service = $this->get('app.pollRepositoryService');
        try {
            $service->deleteById(['id' => $id]);

            return $this->redirect($this->generateUrl('backoffice_polls'));
        } catch (\Exception $e) {
            dump($e->getMessage());
            dump("can't create");
            die();
        }
    }

    /**
     * @Route("/backoffice/polls/{id}/results", name="backoffice_poll_results")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resultsAction($id)
    {
        /** @var PollRepositoryService $service */
        $service = $this->get('app.pollRepositoryService');
        $poll    = $service->getPoll(['id' => $id]);

        $charts = [];
        $questionsAnswers = $service->getResults($id);
        $questionsAnswersAfterTreatment = [];
        $checkId = -1;

        foreach ($questionsAnswers as $questionAnswers) {
            if ($checkId === $questionAnswers['qId']) {
                continue;
            }

            $checkId = $questionAnswers['qId'];

            $props = array_filter($questionsAnswers, function($a) use($checkId) {
                return $a['qId'] === $checkId;
            });

            unset($questionAnswers['propId']);
            unset($questionAnswers['propTitle']);
            unset($questionAnswers['amount']);

            foreach ($props as $j => $prop) {
                $questionAnswers['props'][] =  [
                    'id' => $prop['propId'],
                    'title' => $prop['propTitle'],
                    'amount' => $prop['amount']
                ];
            }

            $questionsAnswersAfterTreatment[] = $questionAnswers;
        }

        foreach ($questionsAnswersAfterTreatment as $question) {
            $chart   = new PieChart();
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
            'poll'    => $poll,
            'charts'  => $charts
        ]);
    }
}
