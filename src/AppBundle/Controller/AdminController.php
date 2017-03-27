<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_homepage")
     * @Route("/admin/", name="admin_homepage_alt")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('@App/AdminUI/index.html.twig');
    }


}
