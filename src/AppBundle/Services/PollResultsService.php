<?php

namespace AppBundle\Services;
use AppBundle\Entity\Page;
use AppBundle\Entity\Poll;
use AppBundle\Entity\User;
use Avegao\ChartjsBundle\Chart\BarChart;
use Avegao\ChartjsBundle\Chart\PieChart;
use Avegao\ChartjsBundle\DataSet\BarDataSet;
use Avegao\ChartjsBundle\DataSet\PieDataSet;
use Liuggio\ExcelBundle\Factory as ExcelService;

/**
 * Class PollResultService
 * @package AppBundle\Services
 */
class PollResultsService
{
    /**
     * @var array
     */
    private $chartColors;

    /**
     * @var ExcelService
     */
    private $excelService;

    /**
     * @var string scriptDirectory
     */
    private $scriptDirectory;

    /**
     * PollResultsService constructor.
     * @param $chartColors
     * @param $rootDir
     * @param $excelService
     */
    public function __construct($chartColors, $rootDir, $excelService)
    {
        $this->chartColors     = $chartColors;
        $this->excelService    = $excelService;
        $this->scriptDirectory = realpath($rootDir . '/../web/CoreUI/js/CustomChartLegend.js');
    }

    /**
     * @param $questionsAnswers
     * @return array
     */
    public function getChartsResults($questionsAnswers)
    {
        $questionsAnswersAfterTreatment = [];
        $charts                         = [];
        $this->getAnswersCount($questionsAnswersAfterTreatment, $questionsAnswers);

        foreach ($questionsAnswersAfterTreatment as $question) {
            /** @var PieChart|BarChart $dataSet */
            $chart = null;
            /** @var PieDataSet|BarDataSet $dataSet */
            $dataSet = null;
            $options = [];
            if ($question['ctTitle'] == 'bar') {
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
            } elseif ($question['ctTitle'] == 'pie') {
                $chart   = new PieChart();
                $dataSet = new PieDataSet();
            }
            $data   = [];
            $labels = [];
            foreach ($question['props'] as $index => $proposition) {
                $labels[] = $proposition['title'];
                $data[]   = $proposition['amount'];
                $dataSet->addBackgroundColor($this->chartColors[$index]);
            }
            $dataSet->setData($data);
            $chart->addDataSet($dataSet);
            $chart->setLabels($labels);
            $chart->generateData();
            $chart->setOptions(array_merge($options, [
                    'legendCallback' => file_get_contents($this->scriptDirectory),
                    'legend'         => ['display' => false]
                ]
            ));
            $charts[] = ['question' => $question, 'chart' => $chart];
        }

        return $charts;
    }

    /**
     * @param Poll $poll
     * @param User $user
     * @param $questionsAnswers
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getExcelResults(Poll $poll, User $user, $questionsAnswers)
    {
        $questionsAnswersAfterTreatment = [];
        $phpExcelObject                 = $this->excelService->createPHPExcelObject();
        $pages                          = $poll->getPages();

        $phpExcelObject->getProperties()
            ->setCreator($user->getFirstName() . ' ' . $user->getLastName())
            ->setTitle($poll->getTitle())
            ->setSubject($poll->getTitle());

        $this->getAnswersCount($questionsAnswersAfterTreatment, $questionsAnswers);

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
            $currentQuestionSheet->getColumnDimension('D')->setWidth(3);
            $currentQuestionSheet->getRowDimension('1')->setRowHeight(30);
            $currentQuestionSheet->getRowDimension('2')->setRowHeight(25);
            $currentQuestionSheet->getRowDimension('3')->setRowHeight(20);
            $currentQuestionSheet->setCellValue('A1', $poll->getTitle());
            $currentQuestionSheet->setCellValue('A2', 'Page ' . ($pageIndex + 1));
            $currentQuestionSheet->setCellValue('A3', $question['qTitle']);
            $currentQuestionSheet->setCellValue('A5', 'Proposition');
            $currentQuestionSheet->setCellValue('B5', 'Quantité');
            $currentQuestionSheet->setCellValue('C5', 'Pourcentage');

            $dataSeriesLabels = [];
            $dataSeriesValues = [];
            foreach ($question['props'] as $index => $proposition) {
                if ($question['ctTitle'] == 'bar') {
                    array_push($dataSeriesLabels, new \PHPExcel_Chart_DataSeriesValues('String', "'" . $currentQuestionSheet->getTitle() . "'" . '!A' . ($index + 6), null, 1));
                    array_push($dataSeriesValues, new \PHPExcel_Chart_DataSeriesValues('Number', "'" . $currentQuestionSheet->getTitle() . "'" . '!B' . ($index + 6), null, 1));
                }
                $currentQuestionSheet->setCellValue('A' . ($index + 6), $proposition['title']);
                $currentQuestionSheet->setCellValue('B' . ($index + 6), $proposition['amount']);
                $currentQuestionSheet->getStyle('C' . ($index + 6))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $currentQuestionSheet->setCellValue(
                    'C' . ($index + 6), intval($proposition['amount'])/$question['sum']
                );
                $currentQuestionSheet->getStyle('A' . ($index + 6))
                    ->applyFromArray([
                        'borders' => [
                            'allborders' => [
                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                            ]
                        ]
                    ]);
                $currentQuestionSheet->getStyle('B' . ($index + 6))
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
            if ($question['ctTitle'] == 'bar') {
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
                $title    = new \PHPExcel_Chart_Title($question['qTitle']);

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

                $chart->setTopLeftPosition('E5');
                $chart->setBottomRightPosition('O34');
                $currentQuestionSheet->addChart($chart);
            } elseif ($question['ctTitle'] == 'pie') {
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

                $chart->setTopLeftPosition('E5');
                $chart->setBottomRightPosition('O34');
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

        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        /** @var \PHPExcel_Writer_Excel2007 $writer */
        $writer = $this->excelService->createWriter($phpExcelObject, 'Excel2007');
        // enable charts
        $writer->setIncludeCharts(true);
        // create the response
        return $this->excelService->createStreamedResponse($writer);
    }

    private function getAnswersCount(&$questionsAnswersAfterTreatment, $questionsAnswers) {
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
                    'id'     => $prop['propId'],
                    'title'  => $prop['propTitle'],
                    'amount' => $prop['amount'],
                ];
                if (key_exists('sum', $questionAnswers)) {
                    $questionAnswers['sum'] += $prop['amount'];
                } else {
                    $questionAnswers['sum'] = $prop['amount'];
                }
            }

            $questionsAnswersAfterTreatment[] = $questionAnswers;
        }
    }
}
