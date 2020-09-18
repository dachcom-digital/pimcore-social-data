<?php

namespace SocialDataBundle\Service;

class EnvironmentService implements EnvironmentServiceInterface
{
    /**
     * @var string
     */
    protected $socialPostDataClass;

    /**
     * @param string $socialPostDataClass
     */
    public function __construct(string $socialPostDataClass)
    {
        $this->socialPostDataClass = $socialPostDataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialPostDataClass()
    {
        return $this->socialPostDataClass;
    }
}
