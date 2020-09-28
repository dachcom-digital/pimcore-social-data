<?php

namespace SocialDataBundle\Manager;

use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Model\WallInterface;

interface SocialPostManagerInterface
{
    /**
     * @param WallInterface $wall
     *
     * @throws \Exception
     */
    public function checkWallStoragePaths(WallInterface $wall);

    /**
     * @param string|int|null $filteredId
     * @param string          $connectorName
     * @param FeedInterface   $feed
     *
     * @return null|Concrete|SocialPostInterface
     */
    public function provideSocialPostEntity($filteredId, string $connectorName, FeedInterface $feed);

    /**
     * @param FeedInterface $feed
     * @param Concrete      $post
     * @param bool          $forceProcessing
     */
    public function persistSocialPostEntity(Concrete $post, FeedInterface $feed, bool $forceProcessing);
}