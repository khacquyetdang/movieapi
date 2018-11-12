<?php

namespace App\Resource\Pagination\Movie;

use App\Resource\Filtering\Movie\MovieFilterDefinition;
use App\Resource\Filtering\Movie\MovieResourceFilter;
use App\Resource\Pagination\Page;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class MoviePagination
{
    /**
     * @var MovieResourceFilter
     */
    private $resourceFilter;

    public function __construct(MovieResourceFilter $resourceFilter)
    {

        $this->resourceFilter = $resourceFilter;
    }

    public function paginate(Page $page, MovieFilterDefinition $filter): PaginatedRepresentation
    {
        $resources = $this->resourceFilter->getResources($filter)
            ->setFirstResult($page->getOffset())
            ->setMaxResults($page->getLimit())
            ->getQuery()
            ->getResult();

        $resourcesCount = $pages = null;
        try {
            $resourcesCount = $this->resourceFilter->getResourceCount($filter)
                ->getQuery()->getSingleScalarResult();
            $pages = ceil($resourcesCount / $page->getLimit());
        } catch (UnexpectedResultException $e) {

        }

        return new PaginatedRepresentation(
            new CollectionRepresentation($resources),
            'get_movies',
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
