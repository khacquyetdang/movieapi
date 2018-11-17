<?php
namespace App\Resource\Filtering;

abstract class AbstractFilterDefinitionFactory implements FilterDefinitionFactoryInterface
{
    public function sortQueryToArray(?string $sortByQuery): ?array
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
        $result = array_intersect_key($result, \array_flip($this->getAcceptedSortFields()));
        return $result;
    }
}
