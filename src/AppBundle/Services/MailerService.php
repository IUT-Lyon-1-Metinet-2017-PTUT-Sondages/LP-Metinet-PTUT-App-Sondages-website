<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 5/13/17
 * Time: 10:19 AM
 */

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MailerService
 * @package AppBundle\Services
 */
class MailerService
{
    /**
     * @var string
     */
    private $mailerUser;

    private $mailer;

    /**
     * @var EntityManager
     */
    private $em;

    private $router;

    private $templating;

    /**
     * Mailer constructor.
     * @param $mailerUser
     * @param \Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     * @param EntityManager $entityManager
     * @param Router $router
     * @param EngineInterface $templating
     */
    public function __construct($mailerUser, \Swift_Mailer $mailer, TranslatorInterface $translator, EntityManager $entityManager, Router $router, EngineInterface $templating)
    {
        $this->mailerUser = $mailerUser;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->em = $entityManager;
        $this->router = $router;
        $this->templating = $templating;
    }

    /**
     * @param User $fromUser
     * @param string $toEmail
     * @param int $id
     * @return bool
     */
    public function sharePoll(User $fromUser, $toEmail, $id)
    {
        $poll = $this->em->getRepository('AppBundle:Poll')->findOneById($id);

        if (isset($poll)) {
            $token = $poll->getAccessToken();
            $url = $this->router->generate('answer_poll', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $mail = \Swift_Message::newInstance();

            $bodyHtml = $this->templating->render('AppBundle:mail:share-poll.html.twig', [
                'user' => $fromUser,
                'url' => $url
            ]);

            $bodyText = $this->templating->render('AppBundle:mail:share-poll.text.twig', [
                'user' => $fromUser,
                'url' => $url
            ]);

            $fromUser->getUsername();

            $mail->setFrom($this->mailerUser)
                ->setTo($toEmail)
                ->setCharset('utf-8')
                ->setPriority(\Swift_Mime_SimpleMessage::PRIORITY_HIGH)
                ->setSubject("[Kodrastan] " . $this->translator->trans('mails.share_poll.subject', [], 'AppBundle'))
                ->setBody($bodyText)
                ->addPart($bodyHtml, 'text/html');

            $this->mailer->send($mail);

            return true;
        }

        return false;
    }
}
