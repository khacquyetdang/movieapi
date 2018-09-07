<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Rest\View()
     */
    public function getMoviesAction()
    {
        $movies = $this->getDoctrine()->getRepository('App:Movie')->findAll();
        return $movies;
    }
}
