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

namespace SocialDataBundle\Model;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;

class ConnectorEngine implements ConnectorEngineInterface
{
    protected int $id = 0;
    protected string $name;
    protected bool $enabled;
    protected ?ConnectorEngineConfigurationInterface $configuration = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled === true;
    }

    public function setConfiguration(ConnectorEngineConfigurationInterface $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): ?ConnectorEngineConfigurationInterface
    {
        return $this->configuration;
    }
}
