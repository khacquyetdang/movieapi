<?php

namespace App\Entity;

use App\Annotation as App;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ORM\Table(name="role")
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *      "person",
 *       href=@Hateoas\Route("get_human", parameters={"person" = "expr(object.getPerson().getId())"})
 *)
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default", "Deserialize"})
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person")
     * @App\DeserializeEntity(type="App\Entity\Person", idField="id", idGetter="getId", setter="setPerson")
     * @Serializer\Groups({"Deserialize"})
     * @Serializer\Expose()
     */
    private $person;

    /**
     * @var string
     * @ORM\Column(name="played_name", type="string", length=100)
     * @Serializer\Groups({"Default", "Deserialize"})
     * @Serializer\Expose()
     */
    private $playedName;

    /**
     * @var Movie
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="roles")
     */
    private $movie;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of person
     *
     * @param  Person  $person
     *
     * @return  self
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Set the value of playedName
     *
     * @param  string  $playedName
     *
     * @return  self
     */
    public function setPlayedName(string $playedName)
    {
        $this->playedName = $playedName;

        return $this;
    }

    /**
     * Set the value of movie
     *
     * @param  Movie  $movie
     *
     * @return  self
     */
    public function setMovie(Movie $movie)
    {
        $this->movie = $movie;

        return $this;
    }

    /**
     * Get the value of person
     *
     * @return  Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Get the value of playedName
     *
     * @return  string
     */
    public function getPlayedName()
    {
        return $this->playedName;
    }

    /**
     * Get the value of movie
     *
     * @return  Movie
     */
    public function getMovie(): Movie
    {
        return $this->movie;
    }
}
