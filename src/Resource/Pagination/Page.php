<?php

namespace App\Resource\Pagination;

class Page
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @param
     * page page
     * limit limt
     */
    public function __construct(int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;

        $this->offset = ($page - 1) * $limit;
    }

    /**
     * Get the value of offset
     *
     * @return  int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Get the value of limit
     *
     * @return  int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get the value of page
     *
     * @return  int
     */
    public function getPage()
    {
        return $this->page;
    }
}
