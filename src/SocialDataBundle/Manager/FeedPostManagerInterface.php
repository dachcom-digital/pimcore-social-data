<?php

namespace SocialDataBundle\Manager;

use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\FeedInterface;

interface FeedPostManagerInterface
{
    public function connectFeedWithPost(FeedInterface $feed, Concrete $socialPost): void;

    public function removePostFromFeeds(Concrete $socialPost): void;
}