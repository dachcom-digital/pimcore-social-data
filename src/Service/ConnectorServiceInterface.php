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

namespace SocialDataBundle\Service;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorServiceInterface
{
    public function installConnector(string $connectorName): ConnectorEngineInterface;

    public function uninstallConnector(string $connectorName): void;

    public function enableConnector(string $connectorName): void;

    public function disableConnector(string $connectorName): void;

    public function connectConnector(string $connectorName): void;

    public function disconnectConnector(string $connectorName): void;

    public function updateConnectorEngineConfiguration(string $connectorName, ConnectorEngineConfigurationInterface $connectorConfiguration): void;

    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false): ConnectorDefinitionInterface;
}
