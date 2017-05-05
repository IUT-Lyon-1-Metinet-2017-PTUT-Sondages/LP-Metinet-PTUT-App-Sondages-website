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

    /**
     * @Route("/backoffice/users/delete/{id}", name="backoffice_users_delete", requirements={"id": "\d+"})
     * @param Request $request
     *
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        if(!$user) {
            $this->addFlash('danger', "Impossible de supprimer l'utilisateur.");
        } else {
            $userManager->deleteUser($user);
            $this->addFlash('success', "L'utilisateur a bien été supprimé !");
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
