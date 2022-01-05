<?php

namespace SocialDataBundle\Service;

class EnvironmentService implements EnvironmentServiceInterface
{
    protected string $socialPostDataClass;

    public function __construct(string $socialPostDataClass)
    {
        $this->socialPostDataClass = $socialPostDataClass;
    }

    public function getSocialPostDataClass(): string
    {
        return $this->socialPostDataClass;
    }
}
