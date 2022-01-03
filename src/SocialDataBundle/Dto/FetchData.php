<?php

namespace SocialDataBundle\Dto;

class FetchData extends AbstractData
{
    protected array $entities;

    public function setFetchedEntities(array $entities): void
    {
        $this->entities = $entities;
    }

    public function getFetchedEntities(): array
    {
        return $this->entities;
    }
}
