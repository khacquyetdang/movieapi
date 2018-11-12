<?php

namespace App\Resource\Filtering\Movie;

class MovieFilterDefinition
{

    private $title;

    private $yearFrom;

    private $yearTo;

    private $timeFrom;

    private $timeTo;

    public function __construct(?string $title, ?int $yearFrom, ?int $yearTo, ?int $timeFrom, ?int $timeTo)
    {
        $this->title = $title;
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
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
        return get_object_vars($this);
    }
}
