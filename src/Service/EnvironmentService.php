<?php

namespace SocialDataBundle\Service;

class EnvironmentService implements EnvironmentServiceInterface
{
    public function __construct(protected string $socialPostDataClass)
    {
    }

    public function getSocialPostDataClass(): string
    {
        return $this->socialPostDataClass;
    }
}
