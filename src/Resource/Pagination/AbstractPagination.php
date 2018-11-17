<?php

namespace App\Resource\Pagination;

use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Pagination\Page;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

abstract class AbstractPagination implements PaginationInterface
{

    public function paginate(Page $page, FilterDefinitionInterface $filter): PaginatedRepresentation
    {
        $resources = $this->getResourceFilter()->getResources($filter)
            ->setFirstResult($page->getOffset())
            ->setMaxResults($page->getLimit())
            ->getQuery()
            ->getResult();

        $resourcesCount = $pages = null;
        try {
            $resourcesCount = $this->getResourceFilter()->getResourceCount($filter)
                ->getQuery()->getSingleScalarResult();
            $pages = ceil($resourcesCount / $page->getLimit());
        } catch (UnexpectedResultException $e) {

        }

        return new PaginatedRepresentation(
            new CollectionRepresentation($resources),
            $this->getRouteName(),
            $filter->getQueryParameters(),
            $page->getPage(),
            $page->getLimit(),
            $pages,
            null,
            null,
            false,
            $resourcesCount
        );
    }
}
