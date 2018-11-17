<?php
namespace App\Resource\Filtering;

interface FilterDefinitionFactoryInterface
{
    public function sortQueryToArray(?string $sortByQuery): ?array;
    public function getAcceptedSortFields(): array;
}
