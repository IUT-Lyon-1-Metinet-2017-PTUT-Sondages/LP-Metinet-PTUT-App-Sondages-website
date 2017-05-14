<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PropositionController extends Controller
{
    /**
     * @Route("/backoffice/proposition/{id}/delete", name="backoffice_proposition_delete")
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->get('app.repository_service.proposition')->deleteById($id);
    }
}
