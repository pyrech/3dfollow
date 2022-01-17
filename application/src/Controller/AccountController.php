<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Data\Exporter;
use App\Entity\Team;
use App\Entity\User;
use App\Form\AccountType;
use App\Security\TokenRefresher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/account", name="account_")
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function index(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenRefresher $tokenRefresher,
        TranslatorInterface $translator,
        Request $request
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isValid = true;

            /** @var string $oldPassword */
            $oldPassword = $form->get('oldPassword')->getData();
            /** @var string $newPassword */
            $newPassword = $form->get('newPassword')->getData();

            if ($newPassword) {
                if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $newPassword
                        )
                    );
                } else {
                    $form->get('oldPassword')->addError(new FormError($translator->trans('validation.old_password_wrong', [], 'validators')));
                    $isValid = false;
                }
            }

            if ($isValid) {
                if ($user->getIsPrinter() && !$user->getTeamCreated()) {
                    $team = new Team();
                    $user->setTeamCreated($team);
                }

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'account.index.flash.success');

                $tokenRefresher->refresh($user, $request);

                return $this->redirectToRoute('account_index');
            }
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/export-data", name="export_data", methods={"GET", "POST"})
     */
    public function exportData(Exporter $exporter, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(FormType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('account/export_data.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $filename = $exporter->export($user);

        return $this
            ->file($filename, '3dfollow-export.zip', ResponseHeaderBag::DISPOSITION_INLINE)
            ->deleteFileAfterSend(true)
        ;
    }
}
