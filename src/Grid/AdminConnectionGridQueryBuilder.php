<?php

namespace EffectConnect\Marketplaces\Grid;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class AdminConnectionGridQueryBuilder
 * @package EffectConnect\Marketplaces\Grid
 */
final class AdminConnectionGridQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('is_active' === $filterName) {
                boolval($filterValue) ?
                    $qb->where('rec.is_active = 1') :
                    $qb->where('rec.is_active = 0');
                continue;
            } elseif('id_shop' === $filterName) { // Prevent ambiguous 'id_shop' in WHERE
                $qb->andWhere("rec.$filterName LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }
            $qb->andWhere("$filterName LIKE :$filterName");
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(rec.id_connection)');

        return $qb;
    }

    /**
     * Base query is the same for both searching and counting
     *
     * @return QueryBuilder
     */
    private function getBaseQuery()
    {
        $selectArray = [
            'rec.id_connection',
            'rec.is_active',
            'rec.name',
            'rec.id_shop',
            'rec.public_key',
            'rec.secret_key',
            'shop.name AS shop_name',
        ];

        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'ec_connection', 'rec')
            ->select(implode(', ', $selectArray))
            ->leftJoin(
                'rec',
                $this->dbPrefix . 'shop',
                'shop',
                'shop.id_shop = rec.id_shop'
            )
        ;
    }
}
