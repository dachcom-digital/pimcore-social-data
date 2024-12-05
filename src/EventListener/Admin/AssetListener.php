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
