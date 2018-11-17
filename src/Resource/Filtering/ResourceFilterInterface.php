<?php
namespace App\Resource\Filtering;

use Doctrine\ORM\QueryBuilder;

interface ResourceFilterInterface
{
    public function getResources($filter): QueryBuilder;
    public function getResourceCount($filter): QueryBuilder;
}
