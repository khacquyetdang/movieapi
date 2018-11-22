<?php
namespace App\Resource\Filtering;

abstract class AbstractFilterDefinition implements FilterDefinitionInterface
{
    private const QUERY_PARAMS_BLACKLIST = ['sortedByArray'];

    public function getQueryParamsBlacklist(): array
    {
        return self::QUERY_PARAMS_BLACKLIST;
    }

    public function getQueryParameters(): array
    {
        return array_diff_key($this->getParameters(), \array_flip(self::QUERY_PARAMS_BLACKLIST));
    }

    public function __toString()
    {
        return \implode($this->getParameters(), "_");
    }

}
