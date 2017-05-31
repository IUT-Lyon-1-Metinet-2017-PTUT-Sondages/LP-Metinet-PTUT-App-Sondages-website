<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 5/13/17
 * Time: 10:19 AM
 */

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\MonologBundle\SwiftMaileruse Symfony\Component\Routing\Router;

/**
 * Class MailerService
 * @package AppBundle\Services
 */
class MailerService
{
    private $mailer;

    /**
     * @var EntityManager
     */
    private $em;

    private $router;

    /**
     * Mailer constructor.
     * @param SwiftMailer $mailer
     */
    public function __construct (SwiftMailer $mailer, EntityManager $entityManager,Router $router)
    {
        $this->mailer = $mailer;
        $this->em = $entityManager;
        $this->router = $router;
    }

    public function sharePoll($mail, $id){
        $poll = $this->em->getRepository('AppBundle:Poll')->findOneById($id);

        if(isset($poll)){
            $token = $poll->getToken();
            $url   =
        }

    }



}
