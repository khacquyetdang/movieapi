<?php
namespace App\Resource\Filtering;

interface SortableFilterDefinitionInterface
{
    public function getSortedByArray(): ?array;
    public function getSortedBy(): ?string;

}
