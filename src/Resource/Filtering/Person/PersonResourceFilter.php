<?php

namespace App\Resource\Filtering\Person;

use App\Repository\PersonRepository;
use App\Resource\Filtering\ResourceFilterInterface;
use Doctrine\ORM\QueryBuilder;

class PersonResourceFilter implements ResourceFilterInterface
{

    /**
     * @var PersonRepository
     */
    private $repository;

    public function __construct(PersonRepository $repository)
    {

        $this->repository = $repository;
    }

    /**
     * @param PersonFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResources($filter): QueryBuilder
    {
        $qb = $this->getQuery($filter);
        $qb->select('person');

        return $qb;
    }

    /**
     * @param PersonFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResourceCount($filter): QueryBuilder
    {

        $qb = $this->getQuery($filter);
        $qb->select('count(person)');

        return $qb;
    }

    /**
     * @param PersonFilterDefinition $filter
     * @return QueryBuilder
     */
    private function getQuery(PersonFilterDefinition $filter): QueryBuilder
    {
        $qb = $this->repository->createQueryBuilder('person');

        if (null !== $filter->getFirstName()) {
            $qb->where(
                $qb->expr()->like('person.firstName', ':firstName')
            );
            $qb->setParameter('firstName', "%{$filter->getFirstName()}%");

        }

        if (null !== $filter->getLastName()) {

            $qb->andWhere(
                $qb->expr()->like('person.lastName', ':lastName')
            );
            $qb->setParameter('lastName', "%{$filter->getLastName()}%");

        }

        if (null !== $filter->getBirthFrom()) {

            $qb->andWhere(
                $qb->expr()->gte('person.dateOfBirth', ':birthFrom')
            );
            $qb->setParameter('birthFrom', "{$filter->getBirthFrom()}");

        }

        if (null !== $filter->getBirthTo()) {

            $qb->andWhere(
                $qb->expr()->lte('person.dateOfBirth', ':birthTo')
            );
            $qb->setParameter('birthTo', "{$filter->getBirthTo()}");

        }

        if (null !== $filter->getSortedByArray()) {
            foreach ($filter->getSortedByArray() as $by => $order) {
                $expr = 'desc' === $order ?
                $qb->expr()->desc("person.$by") : $qb->expr()->asc("person.$by");
                $qb->addOrderBy($expr);
            }
        }

        return $qb;

    }
}
