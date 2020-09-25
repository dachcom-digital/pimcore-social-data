<?php

namespace SocialDataBundle\Logger;

class ProviderContextConsoleProcessor
{
    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        $connector = isset($record['context']['connector']) ? $record['context']['connector'] : '--';

        $record['extra'] = [$connector];

        return $record;
    }
}
