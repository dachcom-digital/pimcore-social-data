<?php

namespace SocialDataBundle\Manager;

use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Model\WallInterface;

interface SocialPostManagerInterface
{
    /**
     * @throws \Exception
     */
    public function checkWallStoragePaths(WallInterface $wall): void;

    public function provideSocialPostEntity(string|int|null $filteredId, string $connectorName, FeedInterface $feed): ?SocialPostInterface;

    public function persistSocialPostEntity(Concrete $post, FeedInterface $feed, bool $forceProcessing): void;
}