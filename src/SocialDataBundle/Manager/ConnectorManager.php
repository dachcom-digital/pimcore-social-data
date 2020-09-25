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
    /**
     * @var array
     */
    protected $availableConnectors;

    /**
     * @var ConnectorDefinitionRegistryInterface
     */
    protected $connectorDefinitionRegistry;

    /**
     * @var ConnectorEngineRepositoryInterface
     */
    protected $connectorEngineRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param array                                $availableConnectors
     * @param ConnectorDefinitionRegistryInterface $connectorDefinitionRegistry
     * @param ConnectorEngineRepositoryInterface   $connectorEngineRepository
     * @param EntityManagerInterface               $entityManager
     */
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

    /**
     * {@inheritdoc}
     */
    public function getAllActiveConnectorDefinitions()
    {
        return array_filter(
            $this->getAllConnectorDefinitions(true),
            function (ConnectorDefinitionInterface $connectorDefinition) {
                return $connectorDefinition->engineIsLoaded() && $connectorDefinition->isConnected();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getAllConnectorDefinitions(bool $loadEngine = false)
    {
        $definitions = [];
        $allConnectorDefinitions = $this->connectorDefinitionRegistry->getAll();

        foreach ($allConnectorDefinitions as $connectorDefinitionName => $connectorDefinition) {

            if (!in_array($connectorDefinitionName, $this->availableConnectors)) {
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

    /**
     * {@inheritdoc}
     */
    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false)
    {
        if (!in_array($connectorDefinitionName, $this->availableConnectors)) {
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

    /**
     * {@inheritdoc}
     */
    public function getEngineById(int $id)
    {
        return $this->connectorEngineRepository->findById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getEngineByName(string $connectorName)
    {
        return $this->connectorEngineRepository->findByName($connectorName);
    }

    /**
     * {@inheritdoc}
     */
    public function createNewEngine(string $connectorName, bool $persist = true)
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

    /**
     * {@inheritdoc}
     */
    public function updateEngine(ConnectorEngineInterface $connector)
    {
        $this->entityManager->persist($connector);
        $this->entityManager->flush();

        return $connector;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteEngine(ConnectorEngineInterface $connector)
    {
        $this->entityManager->remove($connector);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteEngineByName(string $connectorName)
    {
        $connector = $this->getEngineByName($connectorName);
        if (!$connector instanceof ConnectorEngineInterface) {
            return;
        }

        $this->entityManager->remove($connector);
        $this->entityManager->flush();
    }
}
