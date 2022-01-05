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
    protected FeedPostManager $feedPostManager;

    public function __construct(FeedPostManager $feedPostManager)
    {
        $this->feedPostManager = $feedPostManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::POST_DELETE => ['onPostDelete'],
        ];
    }

    public function onPostDelete(DataObjectEvent $event): void
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
