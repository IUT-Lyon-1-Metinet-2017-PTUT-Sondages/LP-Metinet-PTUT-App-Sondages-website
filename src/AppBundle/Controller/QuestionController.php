<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Question;
use AppBundle\Form\QuestionType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class QuestionController
 * @package AppBundle\Controller
 */
class QuestionController extends Controller
{


    /**
     * @Route("/admin/add-question", name="admin_add_question")
     * @param Request $request
     * @return html
     */
    public function addAction(Request $request)
    {
        $service = $this->container->get('app.questionRepositoryService');

        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();
            $service->createQuestion($question, $user);
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'All your changes have been saved');
            return $this->redirect($this->generateUrl('admin_questions'));
        }
        return $this->render('@App/AdminUI/Question/add.html.twig', array('form' => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/edit-question/{id}", name="admin_edit_question")
     */
    public function editAction($id)
    {
        $service = $this->container->get('app.questionRepositoryService');
        $questions = $service->getQuestion(['id'=>$id]);

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();
            $service->createQuestion($question, $user);
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'All your changes have been saved');
            return $this->redirect($this->generateUrl('admin_questions'));
        }
        return $this->render('@App/AdminUI/Question/add.html.twig', array('form' => $form->createView(),
        ));
    }
}
