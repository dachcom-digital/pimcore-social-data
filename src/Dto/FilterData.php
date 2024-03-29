<?php

namespace SocialDataBundle\Dto;

class FilterData extends AbstractData
{
    protected ?array $filteredElement = null;
    protected string|int|null $filteredId = null;

    public function getTransferredData(): mixed
    {
        return $this->transferredData;
    }

    public function setFilteredElement(array $filteredElement): void
    {
        $this->filteredElement = $filteredElement;
    }

    public function getFilteredElement(): ?array
    {
        return $this->filteredElement;
    }

    public function setFilteredId(string|int $filteredId): void
    {
        $this->filteredId = $filteredId;
    }

    public function getFilteredId(): string|int|null
    {
        return $this->filteredId;
    }
}
