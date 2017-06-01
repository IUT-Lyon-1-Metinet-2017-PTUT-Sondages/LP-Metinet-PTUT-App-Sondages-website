<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class BackofficeController extends Controller
{
    /**
     * Display the backoffice.
     * @Route("/backoffice", name="backoffice")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $dataService = $this->get('app.repository_service.dashboard');
        $data = $dataService->getDashboardData($this->getUser());

        return $this->render('@App/backoffice/index.html.twig', ['data' => $data]);
    }
}
