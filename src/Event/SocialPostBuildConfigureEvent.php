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

namespace SocialDataBundle\Event;

use SocialDataBundle\Dto\BuildConfig;
use Symfony\Contracts\EventDispatcher\Event;

class SocialPostBuildConfigureEvent extends Event
{
    protected array $options;
    protected string $connectorName;
    protected BuildConfig $buildConfig;

    public function __construct(string $connectorName, BuildConfig $buildConfig)
    {
        $this->options = [];
        $this->connectorName = $connectorName;
        $this->buildConfig = $buildConfig;
    }

    public function getConnectorName(): string
    {
        return $this->connectorName;
    }

    public function getBuildConfig(): BuildConfig
    {
        return $this->buildConfig;
    }

    public function setOption(mixed $key, mixed $value): void
    {
        $this->options[$key] = $value;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
