<?php

namespace SocialDataBundle\EventListener;

use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Manager\FeedPostManager;
use SocialDataBundle\Model\SocialPostInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeedPostListener implements EventSubscriberInterface
{
    /**
     * @var FeedPostManager
     */
    protected $feedPostManager;

    /**
     * @param FeedPostManager $feedPostManager
     */
    public function __construct(FeedPostManager $feedPostManager)
    {
        $this->feedPostManager = $feedPostManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::POST_DELETE => ['onPostDelete'],
        ];
    }

    /**
     * @param DataObjectEvent $event
     */
    public function onPostDelete(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if (!$object instanceof SocialPostInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        $this->feedPostManager->removePostFromFeeds($object);
    }
}
