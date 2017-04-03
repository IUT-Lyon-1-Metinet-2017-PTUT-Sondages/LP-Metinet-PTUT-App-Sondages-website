<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Poll;
use AppBundle\Form\PollType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class PollController
 * @package AppBundle\Controller
 */
class PollController extends Controller
{
    /**
     * @Route("/admin/polls", name="admin_polls")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(/** @noinspection PhpUnusedParameterInspection */  Request $request)
    {
        $service = $this->container->get('app.pollRepositoryService');
        $polls = $service->getPolls([]);
        // replace this example code with whatever you need
        return $this->render('@App/AdminUI/Poll/index.html.twig', [
                'polls' => $polls,
            ]);
    }

    /**
     * @Route("/admin/add-poll", name="admin_add_poll")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $service = $this->container->get('app.pollRepositoryService');

        $poll = new Poll();
        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $poll = $form->getData();
            $service->createPoll($poll, $user);
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'All your changes have been saved');
            return $this->redirect($this->generateUrl('admin_polls'));
        }
        return $this->render('@App/AdminUI/Poll/add.html.twig', array('form' => $form->createView(),
            ));
    }

    /**
     * @Route("/admin/edit-poll/{id}", name="admin_edit_poll")
     */
    public function editAction(Request $request, $id)
    {
        $service = $this->container->get('app.pollRepositoryService');
        $polls = $service->getPoll(['id'=>$id]);

        // replace this example code with whatever you need
        return $this->render('@App/AdminUI/Poll/index.html.twig', [
                'polls' => $polls,
            ]);
    }
}
