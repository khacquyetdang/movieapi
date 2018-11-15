<?php

namespace App\Resource\Filtering\Movie;

class MovieFilterDefinition
{
    private const QUERY_PARAMS_BLACKLIST = ['sortedByArray'];

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

    public function getQueryParameters(): array
    {
        return array_diff_key(get_object_vars($this), \array_flip(self::QUERY_PARAMS_BLACKLIST));
    }

    /**
     * Get the value of sortedByArray
     */
    public function getSortedByArray()
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
    public function getSortedBy()
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
}
