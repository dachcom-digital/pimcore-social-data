<?php

namespace SocialDataBundle\Factory;

use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Service\EnvironmentService;

class SocialPostFactory implements SocialPostFactoryInterface
{
    public function __construct(protected EnvironmentService $environmentService)
    {
    }

    public function create(): SocialPostInterface
    {
        $objectClass = sprintf('\Pimcore\Model\DataObject\%s', ucfirst($this->environmentService->getSocialPostDataClass()));

        return new $objectClass();
    }
}
