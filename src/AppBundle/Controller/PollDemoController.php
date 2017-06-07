<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PollDemoController extends Controller
{
    /**
     * @Route("/poll/demo")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@App/polls_demo/index.html.twig');
    }

    /**
     * @Route("/poll/demo/page/1")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function page1Action()
    {
        return $this->render('@App/polls_demo/page1.html.twig');
    }

    /**
     * @Route("/poll/demo/page/2")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function page2Action()
    {
        return $this->render('@App/polls_demo/page2.html.twig');
    }

    /**
     * @Route("/poll/demo/end")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function endAction()
    {
        return $this->render('@App/polls_demo/end.html.twig');
    }
}
