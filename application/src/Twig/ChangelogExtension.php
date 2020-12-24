<?php

namespace App\Twig;

use App\Entity\Changelog;
use App\Entity\User;
use App\Repository\ChangelogRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ChangelogExtension extends AbstractExtension
{
    private TokenStorageInterface $tokenStorage;
    private ChangelogRepository $changelogRepository;

    public function __construct(TokenStorageInterface $tokenStorage, ChangelogRepository $changelogRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->changelogRepository = $changelogRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_new_changelogs', [$this, 'getNew']),
        ];
    }

    /**
     * @return Changelog[]
     */
    public function getNew(): array
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return [];
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return [];
        }

        return $this->changelogRepository->getNewChangelogs($user->getLastChangelogSeenAt() ?: $user->getCreatedAt());
    }
}
