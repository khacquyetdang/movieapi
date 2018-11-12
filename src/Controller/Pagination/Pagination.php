<?php

namespace App\Controller\Pagination;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class Pagination
{
    private const KEY_LIMIT = 'limit';
    private const KEY_PAGE = 'page';
    private const DEFAULT_LIMIT = 5;
    private const DEFAULT_PAGE = 1;

    /**
     * @var RegistryInterface
     */
    private $doctrineRegistry;

    public function __construct(RegistryInterface $registry)
    {

        $this->doctrineRegistry = $registry;
    }

    public function paginate(Request $request,
        string $entityName,
        array $criteria,
        string $countMethod,
        array $countMethodParameters,
        string $route,
        array $routeParameter): PaginatedRepresentation {

        $limit = $request->get(self::KEY_LIMIT, self::DEFAULT_LIMIT);
        $page = $request->get(self::KEY_PAGE, self::DEFAULT_PAGE);

        $offset = ($page - 1) * $limit;

        //* @var MovieRepository $repository
        $repository = $this->doctrineRegistry->getRepository($entityName);
        $ressource = $repository->findBy($criteria, null, $limit, $offset);

        if (!method_exists($repository, $countMethod)) {
            throw new \InvalidArgumentException("Entity repository method $countMethod doess not exist");
        }
        $ressourceCount = $repository->{$countMethod}(...$countMethodParameters);

        $pageCount = (int) ceil($ressourceCount / $limit);

        $collection = new CollectionRepresentation($ressource);

        $paginated = new PaginatedRepresentation(
            $collection,
            $route,
            $routeParameter,
            $page,
            $limit,
            $pageCount
        );
        return $paginated;

    }
}
