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
use Symfony\Component\Translation\DataCollectorTranslator;

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
     * @param \Swift_Mailer $mailer
     * @param DataCollectorTranslator $translator
     * @param EntityManager $entityManager
     * @param Router $router
     * @param EngineInterface $templating
     */
    public function __construct(\Swift_Mailer $mailer, DataCollectorTranslator $translator, EntityManager $entityManager, Router $router, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->em = $entityManager;
        $this->router = $router;
        $this->templating = $templating;
    }

    public function sharePoll($from, $to, $id)
    {
        $poll = $this->em->getRepository('AppBundle:Poll')->findOneById($id);

        if (isset($poll)) {
            $token = $poll->getAccessToken();
            $url = $this->router->generate('answer_poll', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $mail = \Swift_Message::newInstance();
            $bodyHtml = $this->templating->render('AppBundle:mail:share-poll.html.twig', ['url' => $url]);
            $bodyText = $this->templating->render('AppBundle:mail:share-poll.text.twig', ['url' => $url]);

            $mail->setFrom($from)
                ->setTo($to)
                ->setSubject("[Kodrastan] " . $this->translator->trans('mails.share_poll.subject', [], 'AppBundle'))
                ->setBody($bodyText)
                ->addPart($bodyHtml, 'text/html');

            $this->mailer->send($mail);

            return true;


        }
        return false;

    }


}
