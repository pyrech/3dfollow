<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Security\AppLoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * @Route("/account", name="account_")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function index(
        UserPasswordEncoderInterface $passwordEncoder,
        AppLoginFormAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler,
        Request $request
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isValid = true;

            if ($form->get('newPassword')->getData()) {
                if ($passwordEncoder->isPasswordValid($user, $form->get('oldPassword')->getData())) {
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('newPassword')->getData()
                        )
                    );
                } else {
                    $form->get('oldPassword')->addError(new FormError('Veuillez saisir votre mot de passe actuel'));
                    $isValid = false;
                }
            }

            if ($isValid) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Compte mis à jour');

                $providerKey = 'main';
                // create an authenticated token for the User
                $token = $authenticator->createAuthenticatedToken($user, $providerKey);
                // authenticate this in the system
                $guardHandler->authenticateWithToken($token, $request, $providerKey);

                return $this->redirectToRoute('account_index');
            }
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
