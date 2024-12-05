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

namespace SocialDataBundle\Dto;

abstract class AbstractData
{
    protected mixed $transferredData = null;

    public function __construct(
        protected BuildConfig $buildConfig,
        protected array $options
    ) {
    }

    /**
     * @throws \Exception
     */
    public function setTransferredData(mixed $transferredData): void
    {
        if ($this->transferredData !== null) {
            throw new \Exception('Transferred Object already has been set.');
        }

        $this->transferredData = $transferredData;
    }

    public function getBuildConfig(): BuildConfig
    {
        return $this->buildConfig;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
