<?php

namespace App\Resource\Pagination\Movie;

use App\Resource\Filtering\Movie\MovieResourceFilter;
use App\Resource\Filtering\ResourceFilterInterface;
use App\Resource\Pagination\AbstractPagination;
use App\Resource\Pagination\PaginationInterface;

class MoviePagination extends AbstractPagination implements PaginationInterface
{
    private const ROUTE = 'get_movies';
    /**
     * @var MovieResourceFilter
     */
    private $resourceFilter;

    public function __construct(MovieResourceFilter $resourceFilter)
    {

        $this->resourceFilter = $resourceFilter;
    }

    public function getResourceFilter(): ResourceFilterInterface
    {
        return $this->resourceFilter;
    }

    public function getRouteName(): string
    {

        return self::ROUTE;
    }
}
