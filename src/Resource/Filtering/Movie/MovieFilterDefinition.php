<?php

namespace App\Resource\Filtering\Movie;

use App\Resource\Filtering\AbstractFilterDefinition;
use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Filtering\SortableFilterDefinitionInterface;

class MovieFilterDefinition extends AbstractFilterDefinition implements FilterDefinitionInterface, SortableFilterDefinitionInterface
{

    private $title;

    private $yearFrom;

    private $yearTo;

    private $timeFrom;

    private $timeTo;

    private $sortedBy;

    private $sortedByArray;

    public function __construct(?string $title, ?int $yearFrom, ?int $yearTo, ?int $timeFrom, ?int $timeTo,
        ?string $sortedBy, ?array $sortedByArray) {
        $this->title = $title;
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
        $this->sortedByArray = $sortedByArray;
        $this->sortedBy = $sortedBy;
    }

    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the value of yearFrom
     */
    public function getYearFrom()
    {
        return $this->yearFrom;
    }

    /**
     * Get the value of yearTo
     */
    public function getYearTo()
    {
        return $this->yearTo;
    }

    /**
     * Get the value of timeFrom
     */
    public function getTimeFrom()
    {
        return $this->timeFrom;
    }

    /**
     * Get the value of timeTo
     */
    public function getTimeTo()
    {
        return $this->timeTo;
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

    public function getParameters(): array
    {
        return \get_object_vars($this);
    }

}
