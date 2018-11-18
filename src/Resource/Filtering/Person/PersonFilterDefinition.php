<?php

namespace App\Resource\Filtering\Person;

use App\Resource\Filtering\AbstractFilterDefinition;
use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Filtering\SortableFilterDefinitionInterface;

class PersonFilterDefinition extends AbstractFilterDefinition implements FilterDefinitionInterface, SortableFilterDefinitionInterface
{

    /**
     * @var string|null
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $lastName;

    /**
     *@var string|null
     */
    private $birthFrom;

    /**
     *@var string|null
     */
    private $birthTo;

    private $sortedBy;

    private $sortedByArray;

    public function __construct(?string $firtName, ?string $lastName,
        ?string $birthFrom, ?string $birthTo,
        ?string $sortedBy, ?array $sortedByArray) {
        $this->firstName = $firtName;
        $this->lastName = $lastName;
        $this->birthFrom = $birthFrom;
        $this->birthTo = $birthTo;
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

    public function getParameters(): array
    {
        return \get_object_vars($this);
    }

    /**
     * Get the value of firstName
     *
     * @return  string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @param  string|null  $firstName
     *
     * @return  self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     *
     * @return  string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @param  string|null  $lastName
     *
     * @return  self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get *@var string|null
     */
    public function getBirthFrom()
    {
        return $this->birthFrom;
    }

    /**
     * Set *@var string|null
     *
     * @return  self
     */
    public function setBirthFrom($birthFrom)
    {
        $this->birthFrom = $birthFrom;

        return $this;
    }

    /**
     * Get *@var string|null
     */
    public function getBirthTo()
    {
        return $this->birthTo;
    }

    /**
     * Set *@var string|null
     *
     * @return  self
     */
    public function setBirthTo($birthTo)
    {
        $this->birthTo = $birthTo;

        return $this;
    }
}
