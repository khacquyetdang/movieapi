<?php

namespace App\Resource\Filtering\Movie;

use Symfony\Component\HttpFoundation\Request;

class MovieFilterDefinitionFactory
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
    private function sortQueryToArray(?string $sortByQuery): ?array
    {
        if (null === $sortByQuery) {
            return null;
        }
        $result = \array_reduce(\explode(',', $sortByQuery),
            function ($accu, $item) {
                $item = trim($item, ' ');
                list($by, $order) = array_replace(
                    [1 => 'desc'],
                    \explode(' ',
                        \preg_replace('/\s+/', ' ', $item))
                );
                $accu[$by] = $order;
                return $accu;
            }, []);
        $result = array_intersect_key($result, \array_flip(self::ACCEPTED_SORT_FIELDS));
        return $result;
    }
}
