<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BackofficeController extends Controller
{
    /**
     * @Route("/backoffice", name="backoffice")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(/** @noinspection PhpUnusedParameterInspection */  Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('@App/backoffice/index.html.twig');
    }
}
