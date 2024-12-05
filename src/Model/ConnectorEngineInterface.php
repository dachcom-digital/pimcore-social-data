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

interface ConnectorEngineInterface
{
    public function getId(): int;

    public function setName(string $name): void;

    public function getName(): string;

    public function setEnabled(bool $enabled): void;

    public function getEnabled(): bool;

    public function isEnabled(): bool;

    public function setConfiguration(ConnectorEngineConfigurationInterface $configuration): void;

    public function getConfiguration(): ?ConnectorEngineConfigurationInterface;
}
