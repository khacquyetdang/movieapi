<?php
namespace App\Resource\Pagination;

use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Filtering\ResourceFilterInterface;
use Hateoas\Representation\PaginatedRepresentation;

interface PaginationInterface
{
    public function paginate(Page $page, FilterDefinitionInterface $filter): PaginatedRepresentation;
    public function getResourceFilter(): ResourceFilterInterface;
    public function getRouteName(): string;
}
