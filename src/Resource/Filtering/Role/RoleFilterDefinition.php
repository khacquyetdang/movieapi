<?php

namespace App\Resource\Filtering\Role;

use App\Resource\Filtering\AbstractFilterDefinition;
use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Filtering\SortableFilterDefinitionInterface;

class RoleFilterDefinition extends AbstractFilterDefinition implements FilterDefinitionInterface, SortableFilterDefinitionInterface
{

    /**
     * @var string|null
     */
    private $playedName;

    /**
     * @var int|null
     */
    private $movie;

    private $sortedBy;

    private $sortedByArray;

    public function __construct(?string $playedName, ?int $movie,
        ?string $sortedBy, ?array $sortedByArray) {
        $this->playedName = $playedName;
        $this->movie = $movie;
        $this->sortedByArray = $sortedByArray;
        $this->sortedBy = $sortedBy;
    }

    /**
     * Get the value of sortedByArray
     */
    public function getSortedByArray(): ?array
    {
        return $this->sortedByArray;
    }

    /**
     * Set the value of sortedByArray
     *
     * @return  self
     */
    public function setSortedByArray($sortedByArray)
    {
        $this->sortedByArray = $sortedByArray;

        return $this;
    }

    /**
     * Get the value of getSortedBy
     */
    public function getSortedBy(): ?string
    {
        return $this->sortedBy;
    }

    /**
     * Set the value of sortedByQuery
     *
     * @return  self
     */
    public function setSortedBy($sortedByQuery)
    {
        $this->sortedBy = $sortedByQuery;

        return $this;
    }

    /**
     * Set the value of movie
     *
     * @return  self
     */
    public function setMovie($movie)
    {
        $this->movie = $movie;

        return $this;
    }

    /**
     * Get the value of playedName
     */
    public function getPlayedName()
    {
        return $this->playedName;
    }

    /**
     * Set the value of playedName
     *
     * @return  self
     */
    public function setPlayedName($playedName)
    {
        $this->playedName = $playedName;

        return $this;
    }

    /**
     * Get the value of movie
     */
    public function getMovie()
    {
        return $this->movie;
    }

    public function getParameters(): array
    {
        return \get_object_vars($this);
    }

}
