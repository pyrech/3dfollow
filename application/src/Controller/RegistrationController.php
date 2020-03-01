<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppLoginFormAuthenticator;
use App\Security\TokenRefresher;
use App\Team\InvitationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="registration_register")
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        AppLoginFormAuthenticator $authenticator,
        InvitationManager $invitationManager,
        Request $request
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard_index');
        }

        $user = new User();
        $user->setIsPrinter(!$invitationManager->isInvitationInProgress($request));

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            if ($user->getIsPrinter()) {
                $team = new Team();
                $user->setTeamCreated($team);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
