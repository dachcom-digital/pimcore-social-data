<?php

namespace SocialDataBundle\Logger;

use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\WallInterface;

class ProviderContextConsoleProcessor
{
    public function __invoke(array $record): array
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
