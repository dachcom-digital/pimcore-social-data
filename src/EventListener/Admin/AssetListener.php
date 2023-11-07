<?php

namespace SocialDataBundle\EventListener\Admin;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BundleManagerEvents::CSS_PATHS => 'addCssFiles',
            BundleManagerEvents::JS_PATHS  => 'addJsFiles',
        ];
    }

    public function addCssFiles(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/socialdata/css/admin.css'
        ]);
    }

    public function addJsFiles(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/socialdata/js/component/relation.js',
            '/bundles/socialdata/js/component/relationTextField.js',
            '/bundles/socialdata/js/component/connectWindow.js',
            '/bundles/socialdata/js/plugin.js',
            '/bundles/socialdata/js/settingsPanel.js',
            '/bundles/socialdata/js/wallsPanel.js',
            '/bundles/socialdata/js/wall/mainPanel.js',
            '/bundles/socialdata/js/connector/abstract-connector.js',
            '/bundles/socialdata/js/feed/abstract-feed.js',
            '/bundles/socialdata/js/vendor/dataObject.js',
        ]);
    }
}
