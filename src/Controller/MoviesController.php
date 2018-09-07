<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/movies", name="movies")
     * @Rest\View
     */
    public function getMoviesAction()
    {
        $movies = $this->getDoctrine()->getRepository('App\Entity:Movie')->findAll();
        return $movies;
    }
}
