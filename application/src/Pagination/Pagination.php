<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Pagination;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Pagination
{
    public const ITEMS_PER_PAGE = 15;

    public function __construct(
        private readonly PaginatorInterface $paginator,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return PaginationInterface<object>
     */
    public function create(QueryBuilder $qb, int $maxItemsPerPage = self::ITEMS_PER_PAGE): PaginationInterface
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            throw new \RuntimeException('No current request');
        }

        $page = $request->query->getInt('page', 1);

        if ($page < 1) {
            throw new NotFoundHttpException('Invalid page');
        }

        return $this->paginator->paginate(
            $qb,
            $page,
            $maxItemsPerPage
        );
    }
}
