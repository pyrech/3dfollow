<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppLoginFormAuthenticator;
use App\Team\InvitationManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route(path: '/register', name: 'registration_register')]
    public function register(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
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
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // hash the plain password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            /** @var string $locale */
            $locale = $request->attributes->getAlpha('_locale');
            $user->setDefaultLocale($locale);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            if ($response = $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            )
            ) {
                return $response;
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
