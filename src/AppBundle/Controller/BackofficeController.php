<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class BackofficeController extends Controller
{
    /**
     * @Route("/backoffice", name="backoffice")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $dataService = $this->get('app.dashboardRepositoryService');

        $data = $dataService->getDashboardData($this->getUser());

        // replace this example code with whatever you need
        return $this->render('@App/backoffice/index.html.twig', ['data'=>$data]);
    }
}
