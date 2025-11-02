<?php

/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */
declare(strict_types=1);

namespace Iqit\IqitProductFlags\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class FlagRepository extends EntityRepository
{
    public function findByProduct(int $langId, int $shopId, array $productCategories, array $hooks): array
    {
        $now = new \DateTime(); // Pobieranie aktualnej daty i czasu

        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('pf')
            ->addSelect('pfl')
            ->leftJoin('pf.entityLangs', 'pfl', 'WITH', 'pfl.lang = :langId')
            ->leftJoin('pf.categories', 'pfc', 'WITH', 'pfc.categoryId IN (:productCategories)')
            ->leftJoin('pf.shops', 'pfs', 'WITH', 'pfs.id = :shopId')
            ->andWhere('pfl IS NOT NULL')
            ->andWhere('(pfc IS NOT NULL OR pf.categories IS EMPTY)') // Pobieranie elementów bez kategorii
            ->andWhere('pfs IS NOT NULL')
            ->andWhere('(pf.fromDate IS NULL OR pf.fromDate <= :now)')
            ->andWhere('(pf.toDate IS NULL OR pf.toDate >= :now)')
            ->andWhere('pf.enable = 1')
            ->andWhere('pf.hook IN (:hooks)')
            ->setParameter('langId', $langId)
            ->setParameter('productCategories', $productCategories)
            ->setParameter('hooks', $hooks)
            ->setParameter('shopId', $shopId)
            ->setParameter('now', $now)
            ->orderBy('pf.position', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
