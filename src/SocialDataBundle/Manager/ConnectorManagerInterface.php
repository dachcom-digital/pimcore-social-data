<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorManagerInterface
{
    /**
     * @return array|ConnectorDefinitionInterface[]
     */
    public function getAllActiveConnectorDefinitions();

    /**
     * @param bool $loadEngine
     *
     * @return array|ConnectorDefinitionInterface[]
     */
    public function getAllConnectorDefinitions(bool $loadEngine = false);

    /**
     * @param string $connectorDefinitionName
     * @param bool   $loadEngine
     *
     * @return ConnectorDefinitionInterface|null
     */
    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false);

    /**
     * @param int $id
     *
     * @return ConnectorEngineInterface|null
     */
    public function getEngineById(int $id);

    /**
     * @param string $connectorName
     *
     * @return ConnectorEngineInterface|null
     */
    public function getEngineByName(string $connectorName);

    /**
     * @param string $connectorName
     * @param bool   $persist
     *
     * @return ConnectorEngineInterface
     */
    public function createNewEngine(string $connectorName, bool $persist = true);

    /**
     * @param ConnectorEngineInterface $connector
     *
     * @return ConnectorEngineInterface|null
     */
    public function updateEngine(ConnectorEngineInterface $connector);

    /**
     * @param ConnectorEngineInterface $connector
     */
    public function deleteEngine(ConnectorEngineInterface $connector);

    /**
     * @param string $connectorName
     */
    public function deleteEngineByName(string $connectorName);
}
