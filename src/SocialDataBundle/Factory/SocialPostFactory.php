<?php

namespace SocialDataBundle\Factory;

use SocialDataBundle\Service\EnvironmentService;

class SocialPostFactory implements SocialPostFactoryInterface
{
    /**
     * @var EnvironmentService
     */
    protected $environmentService;

    /**
     * @param EnvironmentService $environmentService
     */
    public function __construct(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $objectClass = sprintf('\Pimcore\Model\DataObject\%s', ucfirst($this->environmentService->getSocialPostDataClass()));

        return new $objectClass();
    }
}