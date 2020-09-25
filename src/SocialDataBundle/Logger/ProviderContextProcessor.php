<?php

namespace SocialDataBundle\Logger;

class ProviderContextProcessor
{
    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        $record['extra']['connector'] = isset($record['context']['connector']) ? $record['context']['connector'] : '--';

        return $record;
    }
}
