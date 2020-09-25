<?php

namespace SocialDataBundle\Dto;

class FetchData extends AbstractData
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * @param array $entities
     */
    public function setFetchedEntities(array $entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return array
     */
    public function getFetchedEntities()
    {
        return $this->entities;
    }
}
