<?php

namespace App\Resource\Filtering\Movie;

use App\Resource\Filtering\AbstractFilterDefinitionFactory;
use App\Resource\Filtering\FilterDefinitionFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class MovieFilterDefinitionFactory extends AbstractFilterDefinitionFactory implements FilterDefinitionFactoryInterface
{
    private const ACCEPTED_SORT_FIELDS = ['title', 'year', 'time'];
    public function factory(Request $request): MovieFilterDefinition
    {

        return new MovieFilterDefinition(
            $request->get("title"),
            $request->get('yearFrom'),
            $request->get('yearTo'),
            $request->get('timeFrom'),
            $request->get('timeTo'),
            $request->get('sortedBy'),
            $this->sortQueryToArray($request->get('sortedBy'))
        );
    }
    public function getAcceptedSortFields(): array
    {
        return self::ACCEPTED_SORT_FIELDS;
    }
}
