<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller
{
    /**
     * @Route("/backoffice/users", name="backoffice_users")
     * @param Request $request
     */
    public function indexAction(Request $request) {
        die('UsersController');
    }
}
