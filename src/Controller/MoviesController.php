<?php

namespace App\Controller;

use App\Entity\EntityMerger;
use App\Entity\Movie;
use App\Entity\Role;
use App\Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var EntityMerger
     */
    private $entityMerger;

    /**
     * @param EntityMerger $entityMerger
     */
    public function __construct(EntityMerger $entityMerger)
    {
        $this->entityMerger = $entityMerger;
    }

    /**
     * @Rest\View()
     */
    public function getMoviesAction()
    {
        $movies = $this->getDoctrine()->getRepository('App:Movie')->findAll();
        return $movies;
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
     */
    public function getMovieAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }
        return $movie;
    }

    /**
     * @Rest\View
     */
    public function getMovieRolesAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }
        return $movie->getRoles();
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
     * @Rest\View(statusCode=201)
     * @Rest\NoRoute()
     * @ParamConverter("modifiedMovie", converter="fos_rest.request_body",
     * options={"validator" = {"groups" = {"Patch"}}}
     * )
     */
    public function patchMovieAction(?Movie $movie, Movie $modifiedMovie, ConstraintViolationListInterface $validationErrors)
    {
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
