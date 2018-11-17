<?php

namespace App\Resource\Filtering\Role;

use App\Resource\Filtering\AbstractFilterDefinitionFactory;
use App\Resource\Filtering\FilterDefinitionFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RoleFilterDefinitionFactory extends AbstractFilterDefinitionFactory implements FilterDefinitionFactoryInterface
{
    private const ACCEPTED_SORT_FIELDS = ['playedName', 'movie'];
    public function factory(Request $request, ?int $movie): RoleFilterDefinition
    {

        return new RoleFilterDefinition(
            $request->get("playedName"),
            $movie,
            $request->get('sortedBy'),
            $this->sortQueryToArray($request->get('sortedBy'))
        );
    }
    public function getAcceptedSortFields(): array
    {
        return self::ACCEPTED_SORT_FIELDS;
    }
}
