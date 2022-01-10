# Events

## Available Events

#### SOCIAL_POST_BUILDER_FETCH_CONFIGURE 
Use this event to interact with a given `fetch` configuration by using the `SocialPostBuildConfigureEvent` event object.
 
#### SOCIAL_POST_BUILDER_FETCH_POST
Use this event to interact with `fetched` data by using the `SocialPostBuildEvent` event object.
 
#### SOCIAL_POST_BUILDER_FILTER_CONFIGURE
Use this event to interact with a given `filter` configuration by using the `SocialPostBuildConfigureEvent` event object.

#### SOCIAL_POST_BUILDER_FILTER_POST
Use this event to interact with `filtered` data by using the `SocialPostBuildEvent` event object.

#### SOCIAL_POST_BUILDER_TRANSFORM_CONFIGURE
Use this event to interact with a given `transform` configuration by using the `SocialPostBuildConfigureEvent` event object.

#### ### SOCIAL_POST_BUILDER_TRANSFORM_POST
Use this event to interact with `transformed` data by using the `SocialPostBuildEvent` event object.

***

## Example Event Handling

```yaml
services:
    App\EventListener\SocialDataListener:
        autowire: true
        tags:
            - { name: kernel.event_subscriber }
```

```php
<?php

namespace App\EventListener;

use SocialDataBundle\Dto\FetchData;
use SocialDataBundle\Event\SocialPostBuildConfigureEvent;
use SocialDataBundle\Event\SocialPostBuildEvent;
use SocialDataBundle\SocialDataEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SocialDataListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SocialDataEvents::SOCIAL_POST_BUILDER_FETCH_CONFIGURE     => 'onFetchConfigure',
            SocialDataEvents::SOCIAL_POST_BUILDER_FETCH_POST          => 'onFetchPost',
            SocialDataEvents::SOCIAL_POST_BUILDER_FILTER_CONFIGURE    => 'onFilterConfigure',
            SocialDataEvents::SOCIAL_POST_BUILDER_FILTER_POST         => 'onFilterPost',
            SocialDataEvents::SOCIAL_POST_BUILDER_TRANSFORM_CONFIGURE => 'onTransformConfigure',
            SocialDataEvents::SOCIAL_POST_BUILDER_TRANSFORM_POST      => 'onTransformPost',
        ];
    }

    public function onFetchConfigure(SocialPostBuildConfigureEvent $event)
    {
        $options = $event->getOptions();
        $event->setOption('aConnectorDefinedOptionToOverwrite', 'NEW VALUE');
    }

    public function onFetchPost(SocialPostBuildEvent $event)
    {
        /** @var FetchData $data */
        $data = $event->getData();
        //$data->setFetchedEntities([]);
    }

    public function onFilterConfigure(SocialPostBuildConfigureEvent $event)
    {
        $options = $event->getOptions();
    }

    public function onFilterPost(SocialPostBuildEvent $event)
    {
        $data = $event->getData();
    }

    public function onTransformConfigure(SocialPostBuildConfigureEvent $event)
    {
        $options = $event->getOptions();
    }

    public function onTransformPost(SocialPostBuildEvent $event)
    {
        $data = $event->getData();
        $transferredData = $data->getTransferredData();
        $transformedElement = $data->getTransformedElement();
    }
}
```