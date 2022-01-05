<?php

namespace SocialDataBundle\Event;

use SocialDataBundle\Dto\AbstractData;
use Symfony\Contracts\EventDispatcher\Event;

class SocialPostBuildEvent extends Event
{
    protected string $connectorName;
    protected AbstractData $data;

    public function __construct(string $connectorName, AbstractData $data)
    {
        $this->connectorName = $connectorName;
        $this->data = $data;
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
