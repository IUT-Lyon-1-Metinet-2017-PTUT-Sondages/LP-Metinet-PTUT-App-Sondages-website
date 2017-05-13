<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RegistrationController
 * @package AppBundle\Controller
 */
class SecurityController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function renderLogin(array $data)
    {
        if ($this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', "Impossible d'accéder à cette page.");
            return $this->redirectToRoute('backoffice');
        }

        return parent::renderLogin($data);
    }

    /**
     * {@inheritdoc}
     */
    public function loginAction(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', "Impossible d'accéder à cette page.");
            return $this->redirectToRoute('backoffice');
        }

        return parent::loginAction($request);
    }
}
