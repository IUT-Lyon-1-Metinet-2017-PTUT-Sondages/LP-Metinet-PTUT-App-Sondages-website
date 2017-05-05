<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserUpdateType;
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
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/backoffice/users/update/{id}", name="backoffice_users_update", requirements={"id": "\d+"})
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $userManager = $this->get('fos_user.user_manager');
        /** @var User $user */
        $user = $userManager->findUserBy(['id' => $id]);

        if (!$user) {
            $this->addFlash('danger', "L'utilisateur n'existe pas !");
            return $this->redirectToRoute('backoffice_users');
        }

        $form = $this->createForm(UserUpdateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var User $data */
                $data = $form->getData();
                $user->setFirstName($data->getFirstName());
                $user->setLastName($data->getLastName());
                $userManager->updateUser($user);
                $this->addFlash('success', "L'utilisateur a bien été modifié");
            } else {
                $this->addFlash('danger', "Le formulaire n'est pas valide !");
            }
        } else {
            $form->setData($user);
        }

        return $this->render('@App/backoffice/users/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/backoffice/users/delete/{id}", name="backoffice_users_delete", requirements={"id": "\d+"})
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        if (!$user) {
            $this->addFlash('danger', "Impossible de supprimer l'utilisateur.");
        } else {
            $userManager->deleteUser($user);
            $this->addFlash('success', "L'utilisateur a bien été supprimé !");
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
