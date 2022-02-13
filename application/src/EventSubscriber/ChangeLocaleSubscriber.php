<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChangeLocaleSubscriber implements EventSubscriberInterface
{
    private readonly TokenStorageInterface $tokenStorage;
    private readonly EntityManagerInterface $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->query->get('change_locale')) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        if ($user instanceof User) {
            /** @var string $locale */
            $locale = $request->attributes->get('_locale');
            $user->setDefaultLocale($locale);
            $this->entityManager->flush();
        }

        $url = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo();

        $request->query->remove('change_locale');
        $parameters = $request->query->all();
        $queryString = http_build_query($parameters);

        if ($queryString) {
            $url = '?' . $queryString;
        }

        $event->setResponse(new RedirectResponse($url));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
