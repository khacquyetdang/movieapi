<?php

namespace App\Resource\Filtering\Movie;

use App\Repository\MovieRepository;
use Doctrine\ORM\QueryBuilder;

class MovieResourceFilter
{

    /**
     * @var MovieRepository
     */
    private $repository;

    public function __construct(MovieRepository $repository)
    {

        $this->repository = $repository;
    }

    /**
     * @param MovieFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResources($filter): QueryBuilder
    {
        $qb = $this->getQuery($filter);
        $qb->select('movie');

        return $qb;
    }

    /**
     * @param MovieFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResourceCount($filter): QueryBuilder
    {

        $qb = $this->getQuery($filter);
        $qb->select('count(movie)');

        return $qb;
    }

    /**
     * @param MovieFilterDefinition $filter
     * @return QueryBuilder
     */
    private function getQuery(MovieFilterDefinition $filter): QueryBuilder
    {
        $qb = $this->repository->createQueryBuilder('movie');

        if (null !== $filter->getTitle()) {
            $qb->where(
                $qb->expr()->like('movie.title', ':title')
            );
            $qb->setParameter('title', "%{$filter->getTitle()}%");
        }

        if (null !== $filter->getTimeFrom()) {
            $qb->andWhere(
                $qb->expr()->gte('movie.time', ':timeFrom')
            );
            $qb->setParameter('timeFrom', $filter->getTimeFrom());
        }

        if (null !== $filter->getTimeTo()) {
            $qb->andWhere(
                $qb->expr()->lte('movie.time', ':timeTo')
            );
            $qb->setParameter('timeTo', $filter->getTimeTo());
        }

        if (null !== $filter->getYearFrom()) {
            $qb->andWhere(
                $qb->expr()->gte('movie.year', ':yearFrom')
            );
            $qb->setParameter('yearFrom', $filter->getYearFrom());
        }

        if (null !== $filter->getYearTo()) {
            $qb->andWhere(
                $qb->expr()->lte('movie.year', ':yearTo')
            );
            $qb->setParameter('yearTo', $filter->getYearTo());
        }
        return $qb;

    }
}
