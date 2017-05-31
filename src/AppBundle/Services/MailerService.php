<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 5/13/17
 * Time: 10:19 AM
 */

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;
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

    private $templating;

    /**
     * Mailer constructor.
     * @param SwiftMailer $mailer
     */
    public function __construct (\Swift_Mailer $mailer, EntityManager $entityManager, Router $router, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->em = $entityManager;
        $this->router = $router;
        $this->templating = $templating;
    }

    public function sharePoll($from, $to, $id){
        $poll = $this->em->getRepository('AppBundle:Poll')->findOneById($id);

        if(isset($poll)){
            $token = $poll->getAccessToken();
            $url   = $this->router->generate('answer_poll',['token'=>$token], UrlGeneratorInterface::ABSOLUTE_URL);

            $template = 'AppBundle:mail:share-poll.html.twig';

            $body = $this->templating->render($template, ['url' => $url]);
            $mail =  \Swift_Message::newInstance();


            $mail->setFrom($from)
                ->setTo($to)
                ->setSubject("[Kodrastan]Prennez 2 minutes pour répondre à ce sondage")
                ->setBody($body)
                ->setContentType('text/html');

            $this->mailer->send($mail);

            return true;


        }
        return false;

    }



}
