<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController
 * @package AppBundle\Controller
 */
class RegistrationController extends BaseController
{
    /**
     * Register a new User.
     * @param Request $request
     * @Route("/register", name="fos_user_registration_register")
     * @return Response
     */
    public function registerAction(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', "Impossible d'accéder à cette page.");

            return $this->redirectToRoute('backoffice');
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $captchaIsValid = $this->checkGoogleRecaptcha(
                $this->getParameter('recaptcha_server_secret'),
                $request->get('g-recaptcha-response')
            );
            if ($captchaIsValid && $form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(
                    FOSUserEvents::REGISTRATION_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $response)
                );

                $this->get('session')->getFlashBag()->clear();

                return $response;
            }
            if ($captchaIsValid) {
                $form->addError(new FormError('Le captcha pas valide'));
            }
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render('@FOSUser/Registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Call GoogleRecaptcha API service to validate the registration.
     * @param string $secret
     * @param string $response
     * @return bool
     */
    private function checkGoogleRecaptcha($secret, $response)
    {
        // Si on est en env de test, on retourne directement true.
        // TODO: Au lieu d'une méthode dans le controlleur, utiliser un service
        // du style "GoogleReCaptchaValidator", et qu'on l'utiliserait de la sorte
        // $googleReCaptchaValidator->valid($secret, $response): bool
        if ($this->get('kernel')->getEnvironment() === 'test') {
            return true;
        }

        $params = [
            'secret' => $secret,
            'response' => $response,
        ];

        $url = $this->getParameter('recapatcha_api') . http_build_query($params);

        // Check if curl extension is ready
        if (function_exists('curl_init')) {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_POST => false,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_VERBOSE => 1,
                CURLOPT_HEADER => false,
            ]);

            $response = curl_exec($curl);
        } else {
            $response = file_get_contents($url);
        }

        if (empty($response) || is_null($response)) {
            return false;
        }

        $response = json_decode($response);

        return $response->success;
    }
}
