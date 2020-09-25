<?php

namespace SocialDataBundle\Factory;

use SocialDataBundle\Model\SocialPostInterface;

interface SocialPostFactoryInterface
{
    /**
     * @return SocialPostInterface
     */
    public function create();
}