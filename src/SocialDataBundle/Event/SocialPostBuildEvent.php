<?php

namespace SocialDataBundle\Event;

use SocialDataBundle\Dto\AbstractData;
use SocialDataBundle\Dto\FetchData;
use SocialDataBundle\Dto\FilterData;
use SocialDataBundle\Dto\TransformData;
use Symfony\Contracts\EventDispatcher\Event;

class SocialPostBuildEvent extends Event
{
    /**
     * @var string
     */
    protected $connectorName;

    /**
     * @var FetchData|FilterData|TransformData
     */
    protected $data;

    /**
     * @param string       $connectorName
     * @param AbstractData $data
     */
    public function __construct(string $connectorName, AbstractData $data)
    {
        $this->connectorName = $connectorName;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getConnectorName()
    {
        return $this->connectorName;
    }

    /**
     * @return FetchData|FilterData|TransformData
     */
    public function getData()
    {
        return $this->data;
    }
}
