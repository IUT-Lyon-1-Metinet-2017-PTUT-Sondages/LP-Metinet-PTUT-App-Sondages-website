<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\ProfileDeleteType;
use FOS\UserBundle\Controller\ProfileController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @Route("/backoffice/profile/delete", name="backoffice_profile_delete")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', "Vous ne pouvez pas supprimer votre compte.");

            return $this->redirectToRoute('fos_user_profile_show');
        }

        $form = $this->createForm(ProfileDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $factory = $this->get('security.encoder_factory');

            /** @var User $user */
            $user = $this->getUser();
            $encoder = $factory->getEncoder($user);
            $passwordIsValid = $encoder->isPasswordValid($user->getPassword(), $form->get('password')->getData(), $user->getSalt());

            if ($passwordIsValid) {
                $userManager->deleteUser($user);
                return $this->redirectToRoute('homepage');
            } else {
                $this->addFlash('danger', 'Le mot de passe ne correspond pas.');
                return $this->redirectToRoute('fos_user_profile_show');
            }
        }

        return $this->render('AppBundle:Profile:delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
