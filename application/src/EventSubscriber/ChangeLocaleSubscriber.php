<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChangeLocaleSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;
    private EntityManagerInterface $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->query->get('change_locale')) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return;
        }

        $user->setDefaultLocale($request->attributes->get('_locale'));
        $this->entityManager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
