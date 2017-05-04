<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Model\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends Controller
{
    /**
     * @Route("/backoffice/users", name="backoffice_users")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $userManager->findUsers(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('@App/backoffice/users/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
