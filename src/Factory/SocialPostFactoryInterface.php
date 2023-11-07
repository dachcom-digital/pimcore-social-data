<?php

namespace SocialDataBundle\Factory;

use SocialDataBundle\Model\SocialPostInterface;

interface SocialPostFactoryInterface
{
    public function create(): SocialPostInterface;
}