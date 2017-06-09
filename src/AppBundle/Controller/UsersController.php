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
     * Display a list of User.
     * @Route("/backoffice/users", name="backoffice_users")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $userManager = $this->get('app.user_manager');
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $userManager->findUsersAndSortBy(['roles' => 'desc']),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('@App/backoffice/users/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Update an User by it's id.
     * @Route("/backoffice/users/update/{id}", name="backoffice_users_update", requirements={"id": "\d+"})
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $userManager = $this->get('fos_user.user_manager');
        /** @var User $user */
        $user = $userManager->findUserBy(['id' => $id]);

        if (is_null($user)) {
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

                return $this->redirectToRoute('backoffice_users');
            }
        } else {
            $form->setData($user);
        }

        return $this->render('@App/backoffice/users/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/backoffice/users/elevating_to_admin/{id}", name="backoffice_users_elevating_to_admin", requirements={"id": "\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function elevatingToAdmin(Request $request)
    {
        //TODO: déconnecter l'user

        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserBy(['id' => $request->get('id')]);
        $user->addRole('ROLE_ADMIN');
        $userManager->updateUser($user);

        if (!$request->headers->has('referer')) {
            return $this->redirectToRoute('backoffice_users');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/backoffice/users/lowering_to_user/{id}", name="backoffice_users_lowering_to_user", requirements={"id": "\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loweringToUser(Request $request)
    {
        //TODO: déconnecter l'user
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserBy(['id' => $request->get('id')]);
        $user->removeRole('ROLE_ADMIN');
        $userManager->updateUser($user);

        if (!$request->headers->has('referer')) {
            return $this->redirectToRoute('backoffice_users');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Delete an User by it's id.
     * @Route("/backoffice/users/delete/{id}", name="backoffice_users_delete", requirements={"id": "\d+"})
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        if (is_null($user) || $user->hasRole('ROLE_ADMIN')) {
            $this->addFlash('danger', "Impossible de supprimer l'utilisateur.");
        } else {
            $userManager->deleteUser($user);
            $this->addFlash('success', "L'utilisateur a bien été supprimé !");
        }

        if (($redirectUrl = $request->headers->get('referer')) !== null) {
            return $this->redirect($redirectUrl);
        }

        return $this->redirectToRoute('backoffice_users');
    }
}
