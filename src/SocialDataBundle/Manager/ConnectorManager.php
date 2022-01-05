<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Registry\ConnectorDefinitionRegistryInterface;
use Doctrine\ORM\EntityManagerInterface;
use SocialDataBundle\Model\ConnectorEngine;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Repository\ConnectorEngineRepositoryInterface;

class ConnectorManager implements ConnectorManagerInterface
{
    protected array $availableConnectors;
    protected ConnectorDefinitionRegistryInterface $connectorDefinitionRegistry;
    protected ConnectorEngineRepositoryInterface $connectorEngineRepository;
    protected EntityManagerInterface $entityManager;

    public function __construct(
        array $availableConnectors,
        ConnectorDefinitionRegistryInterface $connectorDefinitionRegistry,
        ConnectorEngineRepositoryInterface $connectorEngineRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->availableConnectors = $availableConnectors;
        $this->connectorDefinitionRegistry = $connectorDefinitionRegistry;
        $this->connectorEngineRepository = $connectorEngineRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllActiveConnectorDefinitions(): array
    {
        return array_filter(
            $this->getAllConnectorDefinitions(true),
            static function (ConnectorDefinitionInterface $connectorDefinition) {
                return $connectorDefinition->engineIsLoaded() && $connectorDefinition->isConnected();
            });
    }

    public function getAllConnectorDefinitions(bool $loadEngine = false): array
    {
        $definitions = [];
        $allConnectorDefinitions = $this->connectorDefinitionRegistry->getAll();

        foreach ($allConnectorDefinitions as $connectorDefinitionName => $connectorDefinition) {

            if (!in_array($connectorDefinitionName, $this->availableConnectors, true)) {
                continue;
            }

            if ($loadEngine === true) {
                $connectorEngine = $this->connectorEngineRepository->findByName($connectorDefinitionName);
                $connectorDefinition->setConnectorEngine($connectorEngine);
            }

            $definitions[$connectorDefinitionName] = $connectorDefinition;
        }

        return $definitions;
    }

    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false): ?ConnectorDefinitionInterface
    {
        if (!in_array($connectorDefinitionName, $this->availableConnectors, true)) {
            return null;
        }

        try {
            $connectorDefinition = $this->connectorDefinitionRegistry->get($connectorDefinitionName);
        } catch (\Exception $e) {
            return null;
        }

        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            return null;
        }

        if ($loadEngine === true) {
            $connectorEngine = $this->connectorEngineRepository->findByName($connectorDefinitionName);
            $connectorDefinition->setConnectorEngine($connectorEngine);
        }

        return $connectorDefinition;
    }

    public function getEngineById(int $id): ?ConnectorEngineInterface
    {
        return $this->connectorEngineRepository->findById($id);
    }

    public function getEngineByName(string $connectorName): ?ConnectorEngineInterface
    {
        return $this->connectorEngineRepository->findByName($connectorName);
    }

    public function createNewEngine(string $connectorName, bool $persist = true): ConnectorEngineInterface
    {
        $connector = new ConnectorEngine();
        $connector->setName($connectorName);
        $connector->setEnabled(false);

        if ($persist === false) {
            return $connector;
        }

        $this->entityManager->persist($connector);
        $this->entityManager->flush();

        return $connector;
    }

    public function updateEngine(ConnectorEngineInterface $connector): ConnectorEngineInterface
    {
        $this->entityManager->persist($connector);
        $this->entityManager->flush();

        return $connector;
    }

    public function deleteEngine(ConnectorEngineInterface $connector): void
    {
        $this->entityManager->remove($connector);
        $this->entityManager->flush();
    }

    public function deleteEngineByName(string $connectorName): void
    {
        $connector = $this->getEngineByName($connectorName);
        if (!$connector instanceof ConnectorEngineInterface) {
            return;
        }

        $this->entityManager->remove($connector);
        $this->entityManager->flush();
    }
}
