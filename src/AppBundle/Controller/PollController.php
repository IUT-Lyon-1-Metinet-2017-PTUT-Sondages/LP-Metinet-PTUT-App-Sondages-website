<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use AppBundle\Entity\Poll;
use AppBundle\Entity\User;
use AppBundle\Exception\InvalidVariantException;
use AppBundle\Exception\ValidationFailedException;
use AppBundle\Services\PollRepositoryService;
use AppBundle\Services\ValidationService;
use Avegao\ChartjsBundle\Chart\BarChart;
use Avegao\ChartjsBundle\Chart\PieChart;
use Avegao\ChartjsBundle\DataSet\BarDataSet;
use Avegao\ChartjsBundle\DataSet\PieDataSet;
use Doctrine\ORM\ORMInvalidArgumentException;
use Liuggio\ExcelBundle\Factory;
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
        /** @var PollRepositoryService $service */
        $service = $this->get('app.repository_service.poll');
        $poll    = $service->getPoll(['id' => $id]);

        $charts                         = [];
        $questionsAnswers               = $service->getResults($id);
        $questionsAnswersAfterTreatment = [];
        $checkId                        = -1;

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
                    'id'     => $prop['propId'],
                    'title'  => $prop['propTitle'],
                    'amount' => $prop['amount'],
                ];
            }

            $questionsAnswersAfterTreatment[] = $questionAnswers;
        }

        foreach ($questionsAnswersAfterTreatment as $question) {
            /** @var PieChart|BarChart $dataSet */
            $chart = null;
            /** @var PieDataSet|BarDataSet $dataSet */
            $dataSet = null;
            $options = [];
            if ($question['qType'] == 'Checkbox' || $question['qType'] == 'LinearScale') {
                $chart             = new BarChart();
                $dataSet           = new BarDataSet();
                $options['scales'] = [
                    'yAxes' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true
                            ]
                        ]
                    ]
                ];
            } elseif ($question['qType'] == 'Radio') {
                $chart   = new PieChart();
                $dataSet = new PieDataSet();
            }
            $data   = [];
            $labels = [];
            foreach ($question['props'] as $index => $proposition) {
                $labels[] = $proposition['title'];
                $data[]   = $proposition['amount'];
                $dataSet->addBackgroundColor($this->getParameter('graph_colors')[$index]);
            }
            $dataSet->setData($data);
            $chart->addDataSet($dataSet);
            $chart->setLabels($labels);
            $chart->generateData();
            $chart->setOptions(array_merge($options, [
                    'legendCallback' =>
                        '
                        var text = [];
                        var sum  = 0.0;
                        var totalAnswer = chart.data.datasets[0].data.reduce((pv, cv) => pv + parseInt(cv, 10), 0);
                        for (var i=0; i<chart.data.datasets[0].data.length; i++) {
                            sum += parseInt(chart.data.labels[i], 10) * parseInt(chart.data.datasets[0].data[i], 10);
                            text.push(\'<tr>\');
                            text.push(\'<td width="16">\');
                            text.push(\'<span style="display:block;width:16px;height:16px;background-color:\' + chart.data.datasets[0].backgroundColor[i] + \'"></span>\');
                            text.push(\'</td>\');
                            text.push(\'<td>\');
                            text.push(chart.data.labels[i]);
                            text.push(\'</td>\');
                            text.push(\'<td>\');
                            text.push(chart.data.datasets[0].data[i]);
                            text.push(\'</td>\');
                            text.push(\'<td>\');
                            text.push(Math.round(chart.data.datasets[0].data[i] / totalAnswer * 10000)/100 + \'&nbsp;%\');
                            text.push(\'</td>\');
                            text.push(\'</tr>\');
                        }
                        text.push(\'<tr>\');
                        text.push(\'<td>\');
                        text.push(\'</td>\');
                        text.push(\'<td>\');
                        text.push(\'<span class="font-weight-bold">Total</span>\');
                        text.push(\'</td>\');
                        text.push(\'<td>\');
                        text.push(totalAnswer);
                        text.push(\'</td>\');
                        text.push(\'<td>\');
                        text.push(\'100&nbsp;%\');
                        text.push(\'</td>\');
                        text.push(\'</tr>\');
                        var averageInput = jQuery(\'.average-linear-\' + chart.canvas.id);
                        if(averageInput !== undefined) {
                            averageInput.html(Math.round(sum/totalAnswer*100)/100);
                        }
                        return text.join(\'\');',
                    'legend'         => ['display' => false]
                ]
            ));
            $charts[] = ['question' => $question, 'chart' => $chart];
        }

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
        /** @var Factory $excelService */
        $excelService   = $this->get('phpexcel');
        $phpExcelObject = $excelService->createPHPExcelObject();
        /** @var User $user */
        $user = $this->getUser();
        /** @var PollRepositoryService $service */
        $service = $this->get('app.repository_service.poll');
        $poll    = $service->getPoll(['id' => $id]);

        $pages = $poll->getPages();

        $phpExcelObject->getProperties()
            ->setCreator($user->getFirstName() . ' ' . $user->getLastName())
            ->setTitle($poll->getTitle())
            ->setSubject($poll->getTitle());
        $questionsAnswers = $service->getResults($id);

        $questionsAnswersAfterTreatment = [];
        $checkId                        = -1;

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
                    'id'     => $prop['propId'],
                    'title'  => $prop['propTitle'],
                    'amount' => $prop['amount'],
                ];
            }

            $questionsAnswersAfterTreatment[] = $questionAnswers;
        }

        $phpExcelObject->removeSheetByIndex(0);

        foreach ($questionsAnswersAfterTreatment as $questionIndex => $question) {
            $pageIndex = 0;

            foreach ($pages as $pageIndex => $page) {
                /** @var Page $page */
                if ($page->getId() == $question['paId']) {
                    break;
                }
            }

            $currentQuestionSheet = new \PHPExcel_Worksheet(
                $phpExcelObject, sprintf('Page %d - Question %d', $pageIndex + 1, $questionIndex + 1)
            );
            // Attach the worksheet to the file
            $phpExcelObject->addSheet($currentQuestionSheet, $questionIndex);
            $currentQuestionSheet->getColumnDimension('A')->setWidth(16);
            $currentQuestionSheet->getColumnDimension('B')->setWidth(16);
            $currentQuestionSheet->getColumnDimension('C')->setWidth(16);
            $currentQuestionSheet->setCellValue('A1', $poll->getTitle());
            $currentQuestionSheet->setCellValue('A2', 'Page n°' . $question ['paId']);
            $currentQuestionSheet->setCellValue('A3', $question['qTitle']);
            $currentQuestionSheet->setCellValue('A5', 'Proposition');
            $currentQuestionSheet->setCellValue('B5', 'Quantité');
            $currentQuestionSheet->setCellValue('C5', 'Pourcentage');

            $dataSeriesLabels = [];
            $dataSeriesValues = [];
            foreach ($question['props'] as $index => $proposition) {
                if ($question['qType'] == 'Checkbox' || $question['qType'] == 'LinearScale') {
                    array_push($dataSeriesLabels, new \PHPExcel_Chart_DataSeriesValues('String', "'" . $currentQuestionSheet->getTitle() . "'" . '!A' . ($index + 6), null, 1));
                    array_push($dataSeriesValues, new \PHPExcel_Chart_DataSeriesValues('Number', "'" . $currentQuestionSheet->getTitle() . "'" . '!B' . ($index + 6), null, 1));
                }
                $currentQuestionSheet->setCellValue('A' . ($index + 6), $proposition['title']);
                $currentQuestionSheet->setCellValue('B' . ($index + 6), $proposition['amount']);
                $currentQuestionSheet->getStyle('A' . ($index + 6))
                    ->applyFromArray([
                        'borders' => [
                            'allborders' => [
                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                            ]
                        ]
                    ]);
                $currentQuestionSheet->getStyle('C' . ($index + 6))
                    ->applyFromArray([
                        'borders' => [
                            'allborders' => [
                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                            ]
                        ]
                    ]);
            }

            $xAxisTickValues = array(
                new \PHPExcel_Chart_DataSeriesValues('String', "'" . $currentQuestionSheet->getTitle() . "'" . '!$B$1', NULL, 1),
            );
            if ($question['qType'] == 'Checkbox' || $question['qType'] == 'LinearScale') {
                $series = new \PHPExcel_Chart_DataSeries(
                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,
                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,
                    range(0, count($dataSeriesValues) - 1),
                    $dataSeriesLabels,
                    $xAxisTickValues,
                    $dataSeriesValues
                );
                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_COL);
                $plotArea = new \PHPExcel_Chart_PlotArea(null, array($series));
                $legend   = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
                $title    = new \PHPExcel_Chart_Title('Graphique ' . $question['qTitle']);

                $chart = new \PHPExcel_Chart(
                    'chart' . $question['qTitle'],
                    $title,
                    $legend,
                    $plotArea,
                    true,
                    0,
                    null,
                    null
                );

                $chart->setTopLeftPosition('F1');
                $chart->setBottomRightPosition('S30');
                $currentQuestionSheet->addChart($chart);
            } elseif ($question['qType'] == 'Radio') {
                $dataSeriesLabels = [new \PHPExcel_Chart_DataSeriesValues('String', "'" . $currentQuestionSheet->getTitle() . "'" . '!$A$6:$A$' . (count($question['props']) + 5), null, count($question['props']) + 1)];
                $dataSeriesValues = [new \PHPExcel_Chart_DataSeriesValues('Number', "'" . $currentQuestionSheet->getTitle() . "'" . '!$B$6:$B$' . (count($question['props']) + 5), null, count($question['props']) + 1)];
                $series           = new \PHPExcel_Chart_DataSeries(
                    \PHPExcel_Chart_DataSeries::TYPE_PIECHART,
                    null,
                    range(0, count($dataSeriesValues) - 1),
                    null,
                    $dataSeriesLabels,
                    $dataSeriesValues
                );
                $layout           = new \PHPExcel_Chart_Layout();
                $layout->setShowVal(true);
                $layout->setShowPercent(true);

                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);
                $plotArea = new \PHPExcel_Chart_PlotArea($layout, array($series));
                $legend   = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
                $title    = new \PHPExcel_Chart_Title('Graphique ' . $question['qTitle']);
                $chart    = new \PHPExcel_Chart(
                    'chart' . $question['qTitle'],
                    $title,
                    $legend,
                    $plotArea,
                    true,
                    0,
                    null,
                    null
                );

                $chart->setTopLeftPosition('F1');
                $chart->setBottomRightPosition('S30');
                $currentQuestionSheet->addChart($chart);
            }

            $currentQuestionSheet->getStyle('A1')->applyFromArray(['font' => [
                'bold' => true,
                'size' => 22,
            ]]);
            $currentQuestionSheet->getStyle('A2')->applyFromArray(['font' => [
                'bold' => true,
                'size' => 18,
            ]]);
            $currentQuestionSheet->getStyle('A3')->applyFromArray(['font' => [
                'bold' => true,
                'size' => 16,
            ]]);
            $currentQuestionSheet->getStyle('A5:C5')
                ->applyFromArray([
                    'borders' => [
                        'allborders' => [
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        ]
                    ],
                    'font'    => [
                        'color' => ['rgb' => 'FFFFFF']
                    ]
                ]);
            $currentQuestionSheet->getStyle('A5:C5')->getFill()->applyFromArray([
                'type'       => \PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => [
                    'rgb' => '538DD5'
                ]
            ]);
        }

        // create the writer
        /** @var \PHPExcel_Writer_Excel2007 $writer */
        $writer = $excelService->createWriter($phpExcelObject, 'Excel2007');
        // enable charts
        $writer->setIncludeCharts(true);
        // create the response
        $response = $excelService->createStreamedResponse($writer);
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
