<?php

namespace SocialDataBundle\Event;

use SocialDataBundle\Dto\AbstractData;
use Symfony\Contracts\EventDispatcher\Event;

class SocialPostBuildEvent extends Event
{
    public function __construct(
        protected string $connectorName,
        protected AbstractData $data
    ) {
    }

    public function getConnectorName(): string
    {
        return $this->connectorName;
    }

    public function getData(): AbstractData
    {
        return $this->data;
    }
}
