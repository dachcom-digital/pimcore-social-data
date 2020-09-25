<?php

namespace SocialDataBundle\Manager;

use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\FeedInterface;

interface FeedPostManagerInterface
{
    /**
     * @param FeedInterface $feed
     * @param Concrete      $socialPost
     */
    public function connectFeedWithPost(FeedInterface $feed, Concrete $socialPost);

    /**
     * @param Concrete $socialPost
     */
    public function removePostFromFeeds(Concrete $socialPost);
}