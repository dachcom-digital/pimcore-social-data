<?php

namespace SocialDataBundle\Manager;

use Pimcore\Db\ZendCompatibility\QueryBuilder;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Factory\SocialPostFactoryInterface;
use SocialDataBundle\Logger\LoggerInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class SocialPostManager implements SocialPostManagerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var FeedPostManagerInterface
     */
    protected $feedPostManager;

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
     * @param FeedPostManagerInterface      $feedPostManager
     * @param SocialPostRepositoryInterface $socialPostRepository
     * @param SocialPostFactoryInterface    $socialPostFactory
     */
    public function __construct(
        LoggerInterface $logger,
        FeedPostManagerInterface $feedPostManager,
        SocialPostRepositoryInterface $socialPostRepository,
        SocialPostFactoryInterface $socialPostFactory
    ) {
        $this->logger = $logger;
        $this->feedPostManager = $feedPostManager;
        $this->socialPostRepository = $socialPostRepository;
        $this->socialPostFactory = $socialPostFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function checkWallStoragePaths(WallInterface $wall)
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
            throw new \Exception(sprintf('Asset storage for wall %d (%s) is not defined', $wall->getId(), $wall->getName()));
        }

        if (!$dataStorageFolder instanceof DataObject\Folder) {
            throw new \Exception(sprintf('Data storage for wall %d (%s) is not defined', $wall->getId(), $wall->getName()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function provideSocialPostEntity($filteredId, string $connectorName, FeedInterface $feed)
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
     * {@inheritDoc}
     */
    public function persistSocialPostEntity(Concrete $post, FeedInterface $feed, bool $forceProcessing)
    {
        if (!$post instanceof SocialPostInterface) {
            return;
        }

        $wall = $feed->getWall();
        $dataStorage = $wall->getDataStorage();
        $dataStorageFolder = DataObject\Folder::getById($dataStorage['id']);

        $isNewPost = empty($post->getId());
        $persistenceState = $isNewPost === true ? 'created' : ($forceProcessing === false ? 'updated' : 'updated [forced]');

        $post->setSocialType($feed->getConnectorEngine()->getName());
        $post->setParent($dataStorageFolder);

        // store media first, if required
        if ($feed->getPersistMedia() === true) {
            $this->persistMedia($feed, $post, $forceProcessing);
        }

        $post->setPublished($feed->getPublishPostImmediately());
        $post->setKey(File::getValidFilename(sprintf('%s-%s', $post->getSocialId(), $feed->getConnectorEngine()->getName())));

        try {
            // @todo: make mandatory check configurable?
            //$post->setOmitMandatoryCheck(true);
            $post->save();
            $this->feedPostManager->connectFeedWithPost($feed, $post);
            $this->logger->info(sprintf('Social post %s (%d) successfully %s', $post->getKey(), $post->getId(), $persistenceState), [$feed]);
        } catch (\Throwable $e) {
            $message = is_object($e->getMessage()) ? 'unkown error' : $e->getMessage();
            $this->logger->error(sprintf('Error while persisting social post %s: %s', $post->getSocialId(), $message), [$feed]);
        }
    }

    /**
     * @param FeedInterface       $feed
     * @param SocialPostInterface $socialPost
     * @param bool                $forceProcessing
     */
    protected function persistMedia(FeedInterface $feed, SocialPostInterface $socialPost, bool $forceProcessing)
    {
        if (empty($socialPost->getPosterUrl()) || !is_string($socialPost->getPosterUrl())) {
            // @todo: how to handle force processing (e.g. delete current asset?)
            $this->logger->debug(sprintf('No poster url given for social post %s', $socialPost->getSocialId()), [$feed]);

            return;
        }

        $imageData = $this->assertImageData($socialPost->getPosterUrl());

        if ($imageData === null) {
            return;
        }

        $asset = $this->provideSocialPostAssetByExternalResource($socialPost, $feed, $imageData);

        if (!$asset instanceof Asset\Image) {
            // @todo: how to handle force processing (e.g. delete current asset?)
            $this->logger->error(
                sprintf('Could not provide asset for social post %s', $socialPost->getSocialId()),
                [$feed]
            );

            return;
        }

        $isNewAsset = empty($asset->getId());
        $persistenceState = $isNewAsset === true ? 'created' : ($forceProcessing === false ? 'updated' : 'updated [forced]');

        // 1. no update required, just assert relation and return
        if ($isNewAsset === false && $forceProcessing === false) {
            $this->logger->debug(sprintf('Asset %s for post %s already exists, just asserting relation', $asset->getFilename(), $socialPost->getSocialId()), [$feed]);
            $socialPost->setPoster($asset);

            return;
        }

        // 2. forced mode enabled or new asset
        // => add new data or try to add new version if data has changed!
        if ($isNewAsset === false && $forceProcessing === true && md5($asset->getData()) === md5($imageData['content'])) {
            $socialPost->setPoster($asset);
            $this->logger->debug(
                sprintf('Asset %s for post %s forced update skipped since no data has been changed', $asset->getFilename(), $socialPost->getSocialId()),
                [$feed]
            );

            return;
        }

        $asset->setData($imageData['content']);

        try {
            $asset->save();
            $socialPost->setPoster($asset);
            $this->logger->info(
                sprintf('Asset %s (%d) for social post %s successfully %s', $asset->getFilename(), $asset->getId(), $socialPost->getSocialId(), $persistenceState),
                [$feed]
            );
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Could not persist media %s for post %s: %s', $asset->getFilename(), $socialPost->getSocialId(), $e->getMessage()),
                [$feed]
            );
        }
    }

    /**
     * @param SocialPostInterface $socialPost
     * @param FeedInterface       $feed
     * @param array               $imageData
     *
     * @return Asset\Image|null
     */
    protected function provideSocialPostAssetByExternalResource(SocialPostInterface $socialPost, FeedInterface $feed, array $imageData)
    {
        $wall = $feed->getWall();
        $assetStorage = $wall->getAssetStorage();
        $assetStorageFolder = Asset\Folder::getById($assetStorage['id']);

        $identifier = sprintf('%s-%s', $socialPost->getSocialId(), $socialPost->getSocialType());
        $cleanExtension = str_replace('jpeg', 'jpg', $imageData['extension']);
        $fileNameWithExtension = sprintf('%s%s%s', File::getValidFilename($identifier), strpos($cleanExtension, '.') === false ? '.' : '', $cleanExtension);

        $listing = new Asset\Listing();
        $listing->addConditionParam('p.data = ?', $identifier);
        $listing->onCreateQuery(function (QueryBuilder $builder) {
            $builder->join(['p' => 'properties'], 'p.cid = assets.id AND p.ctype = "asset" AND p.name = "social_data_identifier"', ['sdi' => 'p.data']);
        });

        $propAssets = $listing->getAssets();

        /**
         * 1. Check if asset exists with property
         * 2. If not, check if asset exists in current path definition to avoid duplicate path issues
         */
        if (count($propAssets) > 0) {
            $assetEntity = $propAssets[0];
        } else {
            $assetEntity = Asset::getByPath(sprintf('%s/%s', $assetStorageFolder->getFullPath(), $fileNameWithExtension));
        }

        if ($assetEntity instanceof Asset\Image) {
            return $assetEntity;
        }

        $assetEntity = new Asset\Image();
        $assetEntity->setFilename($fileNameWithExtension);
        $assetEntity->setParent($assetStorageFolder);
        $assetEntity->setProperty('social_data_identifier', 'text', $identifier, false, false);

        return $assetEntity;
    }

    /**
     * @param string $posterUrl
     *
     * @return array|null
     */
    protected function assertImageData(string $posterUrl)
    {
        $extension = null;
        $imageData = null;

        try {
            $content = $this->getAssetDataFromUrl($posterUrl);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Could not load asset data from url "%s". Error: %s', $posterUrl, $e->getMessage()));
            return null;
        }

        try {
            $imageData = getimagesizefromstring($content);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Could not determinate asset data as image from url "%s". Error: %s', $posterUrl, $e->getMessage()));
            return null;
        }

        if (!is_array($imageData)) {
            $this->logger->error(sprintf('Could not extract image data from url "%s". Maybe it is not a valid image?', $posterUrl));
            return null;
        }

        list($imageType, $imageFormat) = explode('/', $imageData['mime']);

        if ($imageType !== 'image') {
            $this->logger->error(sprintf('Asset should be type of "image" but is type of "%s".', $imageType));
            return null;
        }

        try {
            // @todo: this is the only solution to find the real extension
            // since some connectors will return dynamic pages like "/xy.php?image=xy"
            $extension = image_type_to_extension($imageData[2]);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Could not determinate image extension from url "%s".', $posterUrl));
            return null;
        }

        if (!is_string($extension)) {
            $this->logger->error(sprintf('Could not determinate image extension from url "%s".', $posterUrl));
        }

        $width = $imageData[0];
        $height = $imageData[1];

        if ($width <= 1 && $height <= 1) {
            $this->logger->warning(sprintf('Image width (%dpx) and height (%dpx) are too mall to import from url "%s".', $width, $height, $posterUrl));
            return null;
        }

        return [
            'extension' => $extension,
            'content'   => $content,
            'width'     => $width,
            'height'    => $height
        ];
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws \Exception
     */
    protected function getAssetDataFromUrl($url)
    {
        $data = file_get_contents($url);

        if ($data === false) {
            throw new \Exception(sprintf('Unknown error while loading content from url "%s"', $url));
        }

        return $data;
    }
}
