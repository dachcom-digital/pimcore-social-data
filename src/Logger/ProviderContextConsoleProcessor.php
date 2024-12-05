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

namespace SocialDataBundle\Logger;

use Monolog\LogRecord;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\WallInterface;

class ProviderContextConsoleProcessor
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $extra = [];
        $context = is_array($record['context']) ? $record['context'] : [];

        foreach ($context as $contextRow) {
            if ($contextRow instanceof FeedInterface) {
                $extra['feed'] = $contextRow->getId();
                $extra['wall'] = $contextRow->getWall()->getId();
                $extra['engine'] = $contextRow->getConnectorEngine()->getName();
            } elseif ($contextRow instanceof WallInterface) {
                $extra['wall'] = $contextRow->getId();
            } elseif ($contextRow instanceof ConnectorEngineInterface) {
                $extra['engine'] = $contextRow->getName();
            }
        }

        $record['extra'] = $extra;

        return $record;
    }
}
