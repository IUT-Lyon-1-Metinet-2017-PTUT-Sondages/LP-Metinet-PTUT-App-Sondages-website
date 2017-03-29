<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;

class UserController extends FOSRestController
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
