<?php

namespace App\Resource\Filtering\Person;

use App\Resource\Filtering\AbstractFilterDefinitionFactory;
use App\Resource\Filtering\FilterDefinitionFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PersonFilterDefinitionFactory extends AbstractFilterDefinitionFactory implements FilterDefinitionFactoryInterface
{
    private const ACCEPTED_SORT_FIELDS = ['firstName', 'lastName', 'dateOfBirth'];
    public function factory(Request $request): PersonFilterDefinition
    {

        return new PersonFilterDefinition(
            $request->get("firstName"),
            $request->get("lastName"),
            $request->get("birthFrom"),
            $request->get("birthTo"),
            $request->get('sortedBy'),
            $this->sortQueryToArray($request->get('sortedBy'))
        );
    }
    public function getAcceptedSortFields(): array
    {
        return self::ACCEPTED_SORT_FIELDS;
    }
}
