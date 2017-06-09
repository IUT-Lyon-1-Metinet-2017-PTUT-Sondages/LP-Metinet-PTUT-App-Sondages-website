<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ValidDomain;
use AppBundle\Form\ConfigType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigController
 * @package AppBundle\Controller
 */
class ConfigController extends Controller
{
    /**
     * @Route("/backoffice/config", name="backoffice_config")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em   = $this->get('doctrine.orm.entity_manager');
        $form = $this->createForm(ConfigType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var ValidDomain $data */
                $data = $form->getData();
                $em->persist($data);
                $em->flush();

                $this->addFlash('success', 'Le format a bien été ajouté.');

                return $this->redirectToRoute('backoffice_config');
            }
        }

        $validDomains = $em->getRepository('AppBundle:ValidDomain')->findAll();

        return $this->render('@App/backoffice/config/index.html.twig', [
            'form'         => $form->createView(),
            'validDomains' => $validDomains
        ]);
    }

    /**
     * @Route("backoffice/config/{id}/delete", name="backoffice_config_delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        /** @var EntityManager $em */
        $em                    = $this->get('doctrine.orm.entity_manager');
        $validDomainRepository = $em->getRepository('AppBundle:ValidDomain');
        $validDomains          = $validDomainRepository->findAll();
        $validDomain           = $validDomainRepository->findOneBy(['id' => $id]);

        if (count($validDomains) <= 1) {
            $this->addFlash(
                'danger',
                'Impossible de supprimer le dernier format. Au moins un format doit être présent.'
            );
        } elseif (!$validDomain instanceof ValidDomain) {
            $this->addFlash('danger', 'Aucun format correspondant n\'a été trouvé.');
        } else {
            $em->remove($validDomain);
            $em->flush();
            $this->addFlash('success', 'Le format a bien été supprimé.');
        }

        return $this->redirectToRoute('backoffice_config');
    }
}
