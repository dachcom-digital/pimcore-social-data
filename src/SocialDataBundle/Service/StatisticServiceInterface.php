<?php

namespace SocialDataBundle\Service;

use SocialDataBundle\Model\WallInterface;

interface StatisticServiceInterface
{
    /**
     * @param WallInterface $wall
     *
     * @return array
     */
    public function getWallStatistics(WallInterface $wall);
}
