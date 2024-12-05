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

namespace SocialDataBundle\Manager;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorManagerInterface
{
    /**
     * @return array<int, ConnectorDefinitionInterface>
     */
    public function getAllActiveConnectorDefinitions(): array;

    /**
     * @return array<int, ConnectorDefinitionInterface>
     */
    public function getAllConnectorDefinitions(bool $loadEngine = false): array;

    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false): ?ConnectorDefinitionInterface;

    public function getEngineById(int $id): ?ConnectorEngineInterface;

    public function getEngineByName(string $connectorName): ?ConnectorEngineInterface;

    public function createNewEngine(string $connectorName, bool $persist = true): ConnectorEngineInterface;

    public function updateEngine(ConnectorEngineInterface $connector): ConnectorEngineInterface;

    public function deleteEngine(ConnectorEngineInterface $connector): void;

    public function deleteEngineByName(string $connectorName): void;
}
