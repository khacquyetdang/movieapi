<?php

namespace App\Controller;

use App\Entity\Person;
use App\Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class HumansController extends AbstractController
{
    use ControllerTrait;
    /**
     * @Rest\View()
     */
    public function getHumansAction()
    {
        $movies = $this->getDoctrine()->getRepository('App:Person')->findAll();
        return $movies;
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("person", converter="fos_rest.request_body")
     * @Rest\NoRoute
     */
    public function postHumansAction(Person $person, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($person);
        $em->flush();
        return $person;
    }

    /**
     * @Rest\View
     */
    public function deleteHumanAction(Person $person)
    {
        if (null === $person) {
            return $this->view(null, 404);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();

    }

    /**
     * @Rest\View
     */
    public function getHumanAction(?Person $person)
    {
        if (null === $person) {
            return $this->view(null, 404);
        }
        return $person;
    }
}
