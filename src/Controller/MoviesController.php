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
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
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
     * @var TagAwareAdapter;
     */
    private $cache;
    /**
     * @param EntityMerger $entityMerger
     */
    public function __construct(EntityMerger $entityMerger, MoviePagination $moviePagination, RolePagination $rolePagination,
        TagAwareAdapter $cache) {
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
        //     * @Cache(public=true, maxage=20, smaxage=20, mustRevalidate=true, expires="+20 second"))

        //     * @Security("is_authenticated()")
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $movieFilterDefinitonFactory = new MovieFilterDefinitionFactory();

        /**
         * @var MovieFilterDefinition
         */
        $movieFilterDefiniton = $movieFilterDefinitonFactory->factory($request);
        $key = $movieFilterDefiniton->__toString() . "_" . $page->__toString();
        //return $this->moviePagination->paginate($page, $movieFilterDefiniton);
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        } else {
            $result = $this->moviePagination->paginate($page, $movieFilterDefiniton);
            $item->set($result);
            $item->tag('get_movies');
            $item->expiresAfter(120);
            $this->cache->save($item);
            return $result;
        }
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute
     * @SWG\Post(
     *     tags={"Movie"},
     *     summary="Add a new movie resource",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="body", in="body", required=true,
     *                                 @SWG\Schema(type="array", @Model(type=Movie::class))),
     *     @SWG\Response(response="201", description="Returned when resource
     *                                   created", @SWG\Schema(type="array",
     *                                   @Model(type=Movie::class))),
     *     @SWG\Response(response="400", description="Returned when invalid
     *                                   date posted"),
     *     @SWG\Response(response="401", description="Returned when not
     *                                   authenticated"),
     *     @SWG\Response(response="403", description="Returned when token is
     *                                   invalid or expired")
     * )
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
     * @SWG\Delete(
     *      @SWG\Parameter(name="movie", in="path", type="integer",
     *                                  description="Movie id", required=true),
     *      @SWG\Response(response="404", description="Returned when movie is not found")
     *      @SWG\Response(response="204", description="Returned when movie is deleted correctly")
     * )
     */
    public function deleteMovieAction(Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        $this->cache->deleteItem($movie->getCacheKey());
        $this->cache->invalidateTags(array('get_movies'));

        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();

    }

    /**
     * @Rest\View
     * @Cache(public=true, maxage=10, smaxage=10, mustRevalidate=true, expires="+10 second"))
     * @SWG\Get(
     *     tags={"Movie"},
     *     summary="Gets the movie",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="movie", in="path", type="integer",
     *                                  description="Movie id", required=true),
     *     @SWG\Response(response="200", description="Returned when
     *                                   successful", @SWG\Schema(type="array",
     *                                   @Model(type=Movie::class))),
     *     @SWG\Response(response="404", description="Returned when movie is
     *                                   not found")
     * )
     */
    public function getMovieAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        $key = $movie->getCacheKey();
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        } else {
            $item->set($movie);
            $item->tag($movie->getCacheTag());
            $item->expiresAfter(10);
            $this->cache->save($item);
            return $movie;
        }
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
