<?php

namespace SocialDataBundle\Logger;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\WallInterface;

class ProviderContextConsoleProcessor
{
    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
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
            } elseif ($contextRow instanceof ConnectorEngineConfigurationInterface) {
                $extra['engine'] = $contextRow->getName();
            }
        }

        $record['extra'] = $extra;

        return $record;
    }
}
