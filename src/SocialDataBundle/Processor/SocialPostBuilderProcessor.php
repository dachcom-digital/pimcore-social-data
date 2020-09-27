<?php

namespace SocialDataBundle\Processor;

use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
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
use SocialDataBundle\Factory\SocialPostFactoryInterface;
use SocialDataBundle\Logger\LoggerInterface;
use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Manager\FeedPostManagerInterface;
use SocialDataBundle\Manager\WallManagerInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;
use SocialDataBundle\SocialDataEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialPostBuilderProcessor
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var WallManagerInterface
     */
    protected $wallManager;

    /**
     * @var FeedPostManagerInterface
     */
    protected $feedPostManager;

    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @var SocialPostRepositoryInterface
     */
    protected $socialPostRepository;

    /**
     * @var SocialPostFactoryInterface
     */
    protected $socialPostFactory;

    /**
     * @param LoggerInterface               $logger
     * @param EventDispatcherInterface      $eventDispatcher
     * @param WallManagerInterface          $wallManager
     * @param FeedPostManagerInterface      $feedPostManager
     * @param ConnectorManagerInterface     $connectorManager
     * @param SocialPostRepositoryInterface $socialPostRepository
     * @param SocialPostFactoryInterface    $socialPostFactory
     */
    public function __construct(
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
        WallManagerInterface $wallManager,
        FeedPostManagerInterface $feedPostManager,
        ConnectorManagerInterface $connectorManager,
        SocialPostRepositoryInterface $socialPostRepository,
        SocialPostFactoryInterface $socialPostFactory
    ) {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->wallManager = $wallManager;
        $this->feedPostManager = $feedPostManager;
        $this->connectorManager = $connectorManager;
        $this->socialPostRepository = $socialPostRepository;
        $this->socialPostFactory = $socialPostFactory;
    }

    public function process()
    {
        foreach ($this->wallManager->getAll() as $wall) {
            $this->processWall($wall);
        }
    }

    /**
     * @param WallInterface $wall
     */
    protected function processWall(WallInterface $wall)
    {
        $feeds = $wall->getFeeds();

        if (count($feeds) === 0) {
            return;
        }

        $this->logger->debug(sprintf('Process Wall %s (%s)', $wall->getId(), $wall->getName()), [$wall]);

        if ($this->checkStoragePaths($wall) === false) {
            return;
        }

        foreach ($feeds as $feed) {
            $this->processFeed($feed);
        }
    }

    /**
     * @param FeedInterface $feed
     */
    protected function processFeed(FeedInterface $feed)
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

        $posts = $this->loadFeedPosts($connectorName, $buildConfig, $postBuilder);

        if (count($posts) === 0) {
            return;
        }

        // 4 save
        $this->savePosts($feed, $posts);
    }

    /**
     * @param string                     $connectorName
     * @param BuildConfig                $buildConfig
     * @param SocialPostBuilderInterface $postBuilder
     *
     * @return array
     */
    protected function loadFeedPosts(string $connectorName, BuildConfig $buildConfig, SocialPostBuilderInterface $postBuilder)
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
                $this->logger->debug(sprintf('Element "%s" has been removed during filter process', $filteredId), $logContext);
                continue;
            }

            if (empty($filteredId)) {
                $this->logger->error(sprintf('Could not resolve social post id'), $logContext);
                continue;
            }

            $preFetchedSocialPostEntity = $this->provideSocialPostEntity($filteredId, $connectorName, $buildConfig->getFeed());
            if (!$preFetchedSocialPostEntity instanceof SocialPostInterface) {
                $this->logger->error(sprintf('Could not resolve pre-fetched social post for entity with id "%s"', $filteredId), $logContext);
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

    /**
     * @param string                     $type
     * @param string                     $connectorName
     * @param BuildConfig                $buildConfig
     * @param SocialPostBuilderInterface $socialPostBuilder
     * @param array                      $transferredData
     *
     * @return AbstractData|null
     */
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

    /**
     * @param string|int|null $filteredId
     * @param string          $connectorName
     * @param FeedInterface   $feed
     *
     * @return null
     */
    protected function provideSocialPostEntity($filteredId, string $connectorName, FeedInterface $feed)
    {
        if (empty($filteredId)) {
            return null;
        }

        $postEntity = $this->socialPostRepository->findOneByIdAndSocialType($filteredId, $connectorName, true);

        if ($postEntity instanceof SocialPostInterface && $postEntity instanceof Concrete) {
            // we need to check if post is still connected to current feed id
            $this->feedPostManager->connectFeedWithPost($feed, $postEntity);

            return $postEntity;
        }

        $postEntity = $this->socialPostFactory->create();
        $postEntity->setSocialId($filteredId);

        return $postEntity;
    }

    /**
     * @param FeedInterface             $feed
     * @param array|SocialPostInterface $posts
     */
    protected function savePosts(FeedInterface $feed, array $posts)
    {
        $wall = $feed->getWall();

        $dataStorage = $wall->getDataStorage();
        $dataStorageFolder = DataObject\Folder::getById($dataStorage['id']);

        /** @var Concrete|SocialPostInterface $post */
        foreach ($posts as $post) {

            // store media first, if required
            if ($feed->getPersistMedia() === true) {
                $this->persistMedia($feed, $post);
            }

            $post->setSocialType($feed->getConnectorEngine()->getName());
            $post->setParent($dataStorageFolder);

            if (empty($post->getId())) {
                $post->setPublished(false);
            }

            $post->setKey(File::getValidFilename(sprintf('%s-%s', $post->getSocialId(), $feed->getConnectorEngine()->getName())));

            try {
                $post->save();
                $this->feedPostManager->connectFeedWithPost($feed, $post);
            } catch (\Throwable $e) {

                $this->logger->error(
                    sprintf('Error while saving social post (%s): %s',
                        $post->getSocialId(),
                        is_object($e->getMessage()) ? get_class($e->getMessage()) : $e->getMessage()
                    ),
                    [$feed]
                );

                continue;
            }

            $this->logger->info(
                sprintf('Social post %s (%d) successfully saved', $post->getKey(), $post->getId()),
                [$feed]
            );
        }
    }

    /**
     * @param FeedInterface       $feed
     * @param SocialPostInterface $socialPost
     */
    protected function persistMedia(FeedInterface $feed, SocialPostInterface $socialPost)
    {
        if (empty($socialPost->getPosterUrl())) {
            return;
        }

        $wall = $feed->getWall();

        $assetStorage = $wall->getAssetStorage();
        $assetStorageFolder = Asset\Folder::getById($assetStorage['id']);

        $posterUrl = $socialPost->getPosterUrl();

        try {
            // @todo: this is the only solution to find the real extension
            // since some connectors will return dynamic pages like "/xy.php?image=xy"
            $size = getimagesize($posterUrl);
            $extension = image_type_to_extension($size[2]);
        } catch (\Exception $e) {
            $extension = 'jpg';
        }

        $cleanExtension = str_replace('jpeg', 'jpg', $extension);

        $fileName = sprintf('%s-%s', $socialPost->getSocialId(), $socialPost->getSocialType());
        $fileNameWithExtension = sprintf('%s.%s', File::getValidFilename($fileName), $cleanExtension);

        $image = Asset::getByPath(sprintf('%s/%s', $assetStorageFolder->getFullPath(), $fileNameWithExtension));

        if ($image instanceof Asset\Image) {

            $this->logger->debug(
                sprintf('Asset %s for post %s already exists, just creating relation.', $fileNameWithExtension, $socialPost->getSocialId()),
                [$feed]
            );

            $socialPost->setPoster($image);

            return;
        }

        $poster = new Asset\Image();
        // @todo: should we use guzzle (curl) instead?
        $poster->setData(file_get_contents($posterUrl));
        $poster->setFilename($fileNameWithExtension);
        $poster->setParent($assetStorageFolder);

        try {
            $poster->save();

            $this->logger->info(
                sprintf('Asset %s for social post %s successfully stored', $fileNameWithExtension, $socialPost->getSocialId()),
                [$feed]
            );

        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Could not persist media %s for post %s: %s', $fileNameWithExtension, $socialPost->getSocialId(), $e->getMessage()),
                [$feed]
            );

            return;
        }

        $socialPost->setPoster($poster);
    }

    /**
     * @param WallInterface $wall
     *
     * @return bool
     */
    protected function checkStoragePaths(WallInterface $wall)
    {
        $dataStorageFolder = null;
        $assetStorageFolder = null;
        $assetStorage = $wall->getAssetStorage();
        $dataStorage = $wall->getDataStorage();

        if (is_array($assetStorage)) {
            $assetStorageFolder = Asset\Folder::getById($assetStorage['id']);
        }

        if (is_array($dataStorage)) {
            $dataStorageFolder = DataObject\Folder::getById($dataStorage['id']);
        }

        if (!$assetStorageFolder instanceof Asset\Folder) {
            $this->logger->error(
                sprintf('Asset storage for wall %d (%s) is not defined',
                    $wall->getId(),
                    $wall->getName()
                ),
                [$wall]
            );

            return false;
        }

        if (!$dataStorageFolder instanceof DataObject\Folder) {
            $this->logger->error(
                sprintf('Data storage for wall %d (%s) is not defined',
                    $wall->getId(),
                    $wall->getName()
                ),
                [$wall]
            );

            return false;
        }

        return true;
    }
}
