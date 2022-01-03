<?php

namespace SocialDataBundle\Processor;

use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Dto\AbstractData;
use SocialDataBundle\Dto\BuildConfig;
use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Connector\SocialPostBuilderInterface;
use SocialDataBundle\Dto\FetchData;
use SocialDataBundle\Dto\FilterData;
use SocialDataBundle\Dto\TransformData;
use SocialDataBundle\Event\SocialPostBuildConfigureEvent;
use SocialDataBundle\Event\SocialPostBuildEvent;
use SocialDataBundle\Exception\BuildException;
use SocialDataBundle\Logger\LoggerInterface;
use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Manager\SocialPostManagerInterface;
use SocialDataBundle\Manager\WallManagerInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Service\LockServiceInterface;
use SocialDataBundle\SocialDataEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SocialPostBuilderProcessor
{
    protected LoggerInterface $logger;
    protected LockServiceInterface $lockService;
    protected EventDispatcherInterface $eventDispatcher;
    protected WallManagerInterface $wallManager;
    protected SocialPostManagerInterface $socialPostManager;
    protected ConnectorManagerInterface $connectorManager;

    public function __construct(
        LoggerInterface $logger,
        LockServiceInterface $lockService,
        EventDispatcherInterface $eventDispatcher,
        WallManagerInterface $wallManager,
        SocialPostManagerInterface $socialPostManager,
        ConnectorManagerInterface $connectorManager
    ) {
        $this->logger = $logger;
        $this->lockService = $lockService;
        $this->eventDispatcher = $eventDispatcher;
        $this->wallManager = $wallManager;
        $this->socialPostManager = $socialPostManager;
        $this->connectorManager = $connectorManager;
    }

    public function process(bool $forceProcessing, ?int $wallId): void
    {
        if ($this->lockService->isLocked(LockServiceInterface::SOCIAL_POST_BUILD_PROCESS_ID)) {
            $this->logger->debug(sprintf('Process %s already has been started', LockServiceInterface::SOCIAL_POST_BUILD_PROCESS_ID));
            return;
        }

        if ($wallId !== null) {
            $walls = $this->wallManager->getById($wallId);
            if (!$walls instanceof WallInterface) {
                $this->logger->error(sprintf('Wall with id %d not found', $wallId));
                return;
            }
        } else {
            $walls = $this->wallManager->getAll();
        }

        $walls = is_array($walls) ? $walls : [$walls];

        $this->lockService->lock(LockServiceInterface::SOCIAL_POST_BUILD_PROCESS_ID);

        foreach ($walls as $wall) {
            try {
                $this->processWall($wall, $forceProcessing);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Uncaught exception while processing wall: %s', $e->getMessage()), [$wall]);
            }
        }

        $this->lockService->unlock(LockServiceInterface::SOCIAL_POST_BUILD_PROCESS_ID);
    }

    protected function processWall(WallInterface $wall, bool $forceProcessing): void
    {
        $feeds = $wall->getFeeds();

        if (count($feeds) === 0) {
            return;
        }

        $this->logger->debug(sprintf('Process Wall %s (%s)', $wall->getId(), $wall->getName()), [$wall]);

        try {
            $this->socialPostManager->checkWallStoragePaths($wall);
        } catch (\Exception $e) {
            $this->logger->error(sprintf($e->getMessage()), [$wall]);
            return;
        }

        foreach ($feeds as $feed) {
            $this->processFeed($feed, $forceProcessing);
        }
    }

    protected function processFeed(FeedInterface $feed, bool $forceProcessing): void
    {
        $connectorEngine = $feed->getConnectorEngine();
        if (!$connectorEngine instanceof ConnectorEngineInterface) {
            // @todo: dispatch notification?
            return;
        }

        if (!$connectorEngine->isEnabled()) {
            // @todo: dispatch notification?
            return;
        }

        $connectorName = $connectorEngine->getName();
        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            // @todo: dispatch notification?
            return;
        }

        if (!$connectorDefinition->isConnected()) {
            // @todo: dispatch notification?
            return;
        }

        $this->logger->debug(sprintf('Process Feed %s', $feed->getId()), [$feed]);

        $buildConfig = new BuildConfig($feed, $connectorEngine->getConfiguration(), $connectorDefinition->getDefinitionConfiguration());
        $postBuilder = $connectorDefinition->getSocialPostBuilder();

        $posts = $this->loadFeedPosts($connectorName, $buildConfig, $postBuilder, $forceProcessing);

        if (count($posts) === 0) {
            return;
        }

        // 4 save
        $this->savePosts($feed, $posts, $forceProcessing);
    }

    protected function loadFeedPosts(string $connectorName, BuildConfig $buildConfig, SocialPostBuilderInterface $postBuilder, bool $forceProcessing): array
    {
        $posts = [];
        $logContext = [$buildConfig->getFeed()];

        // 1 fetch
        $fetchData = $this->dispatchSocialPostBuildCycle('fetch', $connectorName, $buildConfig, $postBuilder);

        if (!$fetchData instanceof FetchData) {
            // nothing to log. if this is empty, something happened already and has been logged too.
            return [];
        }

        $fetchedItems = $fetchData->getFetchedEntities();

        if (!is_array($fetchedItems)) {
            $this->logger->debug(sprintf('No elements found during fetch process'), $logContext);
            return [];
        }

        foreach ($fetchedItems as $entry) {

            // 2 filter
            $filterData = $this->dispatchSocialPostBuildCycle('filter', $connectorName, $buildConfig, $postBuilder, [
                'transferredData' => $entry
            ]);

            if (!$filterData instanceof FilterData) {
                // nothing to log. if this is empty, something happened already and has been logged too.
                continue;
            }

            $filteredId = $filterData->getFilteredId();
            $filteredElement = $filterData->getFilteredElement();

            if ($filteredElement === null) {
                $this->logger->debug(sprintf('Element%s has been removed during filter process', empty($filteredId) ? '' : sprintf(' "%s"', $filteredId)), $logContext);
                continue;
            }

            if (empty($filteredId)) {
                $this->logger->error(sprintf('Could not resolve social post id'), $logContext);
                continue;
            }

            $preFetchedSocialPostEntity = $this->socialPostManager->provideSocialPostEntity($filteredId, $connectorName, $buildConfig->getFeed());
            if (!$preFetchedSocialPostEntity instanceof Concrete && !$preFetchedSocialPostEntity instanceof SocialPostInterface) {
                $this->logger->error(sprintf('Could not resolve pre-fetched social post for entity with id "%s"', $filteredId), $logContext);
                continue;
            }

            if (!empty($preFetchedSocialPostEntity->getId()) && $forceProcessing === false) {
                $this->logger->debug(
                    sprintf('Social post %s (%d) already has been processed', $preFetchedSocialPostEntity->getSocialId(), $preFetchedSocialPostEntity->getId()),
                    $logContext
                );
                continue;
            }

            $transformData = $this->dispatchSocialPostBuildCycle('transform', $connectorName, $buildConfig, $postBuilder, [
                'transferredData'  => $filteredElement,
                'socialPostEntity' => $preFetchedSocialPostEntity
            ]);

            if (!$transformData instanceof TransformData) {
                // nothing to log. if this is empty, something happened already and has been logged too.
                continue;
            }

            $transformedEntry = $transformData->getTransformedElement();
            if (!$transformedEntry instanceof SocialPostInterface) {
                $this->logger->debug(sprintf('Element "%s" has been removed during transform process', $filteredId), $logContext);
                continue;
            }

            $posts[] = $transformedEntry;
        }

        return $posts;
    }

    protected function dispatchSocialPostBuildCycle(
        string $type,
        string $connectorName,
        BuildConfig $buildConfig,
        SocialPostBuilderInterface $socialPostBuilder,
        ?array $transferredData = null
    ): ?AbstractData {

        $buildDataTransferObject = null;
        $logContext = [$buildConfig->getFeed()];

        $configureEventName = sprintf('%s::SOCIAL_POST_BUILDER_%s_CONFIGURE', SocialDataEvents::class, strtoupper($type));
        $postEventName = sprintf('%s::SOCIAL_POST_BUILDER_%s_POST', SocialDataEvents::class, strtoupper($type));

        $builderMethod = $type;
        $builderConfigureMethod = sprintf('configure%s', ucfirst($type));

        try {
            $optionsResolver = new OptionsResolver();
            $socialPostBuilder->$builderConfigureMethod($buildConfig, $optionsResolver);

            $configureEvent = new SocialPostBuildConfigureEvent($connectorName, $buildConfig);
            $this->eventDispatcher->dispatch($configureEvent, constant($configureEventName));

            $transferObjectClass = sprintf('SocialDataBundle\Dto\%sData', ucfirst($type));
            /** @var AbstractData $buildDataTransferObject */
            $buildDataTransferObject = new $transferObjectClass($buildConfig, $optionsResolver->resolve($configureEvent->getOptions()));

            // set transfer arguments
            if (is_array($transferredData)) {
                foreach ($transferredData as $transferSetter => $transferRow) {
                    $setter = sprintf('set%s', ucfirst($transferSetter));
                    if (method_exists($buildDataTransferObject, $setter)) {
                        $buildDataTransferObject->$setter($transferRow);
                    }
                }
            }

            $socialPostBuilder->$builderMethod($buildDataTransferObject);

            $postEvent = new SocialPostBuildEvent($connectorName, $buildDataTransferObject);
            $this->eventDispatcher->dispatch($postEvent, constant($postEventName));

            $buildDataTransferObject = $postEvent->getData();

        } catch (BuildException $e) {
            $this->logger->error(sprintf('[Build Error] %s', $e->getMessage()), $logContext);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('[Critical Error] %s', $e->getMessage()), $logContext);
        }

        return $buildDataTransferObject;
    }

    protected function savePosts(FeedInterface $feed, array $posts, bool $forceProcessing): void
    {
        /** @var Concrete|SocialPostInterface $post */
        foreach ($posts as $post) {
            try {
                $this->socialPostManager->persistSocialPostEntity($post, $feed, $forceProcessing);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Error while persisting social post %s: %s', $post->getSocialId(), $e->getMessage()), [$feed]);
            }
        }
    }
}
