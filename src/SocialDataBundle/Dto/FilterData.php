<?php

namespace SocialDataBundle\Dto;

class FilterData extends AbstractData
{
    /**
     * @var array
     */
    protected $filteredElement;

    /**
     * @var int|string
     */
    protected $filteredId;

    /**
     * @return mixed
     */
    public function getTransferredData()
    {
        return $this->transferredData;
    }

    /**
     * @param array $filteredElement
     */
    public function setFilteredElement(array $filteredElement)
    {
        $this->filteredElement = $filteredElement;
    }

    /**
     * @return array
     */
    public function getFilteredElement()
    {
        return $this->filteredElement;
    }

    /**
     * @param string|int $filteredId
     */
    public function setFilteredId($filteredId)
    {
        $this->filteredId = $filteredId;
    }

    /**
     * @return string|int
     */
    public function getFilteredId()
    {
        return $this->filteredId;
    }
}
