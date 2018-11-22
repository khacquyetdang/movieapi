<?php

namespace App\Controller;

use App\Controller\Pagination\Pagination;
use App\Entity\EntityMerger;
use App\Entity\Movie;
use App\Entity\Role;
use App\Exception\ValidationException;
use App\Resource\Filtering\Movie\MovieFilterDefinitionFactory;
use App\Resource\Filtering\Role\RoleFilterDefinitionFactory;
use App\Resource\Pagination\Movie\MoviePagination;
use App\Resource\Pagination\PageRequestFactory;
use App\Resource\Pagination\Role\RolePagination;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Psr\SimpleCache\CacheInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var EntityMerger
     */
    private $entityMerger;

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * @var MoviePagination
     */
    private $moviePagination;

    /**
     * @var RolePagination
     */
    private $rolePagination;

    /**
     * @var CacheInterface;
     */
    private $cache;
    /**
     * @param EntityMerger $entityMerger
     */
    public function __construct(EntityMerger $entityMerger, MoviePagination $moviePagination, RolePagination $rolePagination,
        CacheInterface $cache) {
        $this->entityMerger = $entityMerger;
        $this->moviePagination = $moviePagination;
        $this->rolePagination = $rolePagination;
        $this->cache = $cache;
    }

    /**
     * @Rest\View()
     */
    public function getMoviesAction(Request $request)
    {
        // /     * @Cache(public=true, maxage=1, smaxage=1, mustRevalidate=true, expires="+1 second"))

        //     * @Security("is_authenticated()")
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $movieFilterDefinitonFactory = new MovieFilterDefinitionFactory();

        /**
         * @var MovieFilterDefinition
         */
        $movieFilterDefiniton = $movieFilterDefinitonFactory->factory($request);
        $key = $movieFilterDefiniton->__toString() . "_" . $page->__toString();
        if ($this->cache->get($key) !== null) {
            return $this->cache->get($key);
        } else {
            $result = $this->moviePagination->paginate($page, $movieFilterDefiniton);
            $this->cache->set($key, $result);
            return $result;
        }

    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute
     */
    public function postMoviesAction(Movie $movie, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();
        return $movie;
    }

    /**
     * @Rest\View
     */
    public function deleteMovieAction(Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();

    }

    /**
     * @Rest\View
     * @Cache(public=true, maxage=1, smaxage=1, mustRevalidate=true, expires="+1 second"))
     */
    public function getMovieAction(?Movie $movie)
    {
        //dump($this->cache);
        if (null === $movie) {
            return $this->view(null, 404);
        }
        return $movie;
    }

    /**
     * @Rest\View
     */
    public function getMovieRolesAction(Request $request, ?Movie $movie)
    {
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $roleFilterDefinitionFactory = new RoleFilterDefinitionFactory();

        $roleFilterDefinition = $roleFilterDefinitionFactory->factory($request, $movie->getId());

        return $this->rolePagination->paginate($page, $roleFilterDefinition);
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("role", converter="fos_rest.request_body",
     *      options={"deserializationContext"={"groups"={"Deserialize"}}})
     * @Rest\NoRoute
     */
    public function postMovieRolesAction(Movie $movie, Role $role, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $role->setMovie($movie);
        $em = $this->getDoctrine()->getManager();
        $em->persist($role);
        $movie->getRoles()->add($role);

        $em->persist($movie);
        $em->flush();
        return $role;
    }

    /**
     * @Rest\NoRoute()
     * @ParamConverter("modifiedMovie", converter="fos_rest.request_body",
     * options={"validator" = {"groups" = {"Patch"}}}
     * )
     * @Security("is_authenticated()")
     */
    public function patchMovieAction(?Movie $movie, Movie $modifiedMovie, ConstraintViolationListInterface $validationErrors)
    {
        //
        if (null === $movie) {
            return $this->view(null, 404);
        }
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        // merge entities

        $this->entityMerger->merge($movie, $modifiedMovie);

        // Persist
        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);

        $em->flush();

        return $movie;

    }
}
