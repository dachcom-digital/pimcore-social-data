<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace SocialDataBundle\EventListener;

use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Manager\FeedPostManager;
use SocialDataBundle\Model\SocialPostInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeedPostListener implements EventSubscriberInterface
{
    public function __construct(protected FeedPostManager $feedPostManager)
    {
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
