<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 * @Hateoas\Relation(
 *      "roles",
 *       href=@Hateoas\Route("get_movie_roles", parameters={"movie" = "expr(object.getId())"})
 *)
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups("Default")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Length(max=255)
     */
    private $title;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank(groups="Default")
     * @Assert\Range(min=1888, groups={"Default", "Patch"})
     */
    private $year;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Range(min=11, max=300, groups={"Default", "Patch"})
     */
    private $time;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(groups={"Default", "Patch"})
     */
    private $description;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Role", mappedBy="movie", cascade={"remove"})
     * @Serializer\Exclude()
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of roles
     *
     * @return  Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @param  ArrayCollection  $roles
     *
     * @return  self
     */
    public function setRoles(ArrayCollection $roles)
    {
        $this->roles = $roles;

        return $this;
    }
    /**
     * @return string
     */
    public function getCacheKey()
    {
        $reflect = new \ReflectionClass($this);
        //dump($reflect->getShortName());
        return $reflect->getShortName() . "_" . $this->id;
    }
    public function getCacheTag()
    {
        $reflect = new \ReflectionClass($this);
        //dump($reflect->getShortName());
        return $reflect->getShortName();
    }

}
