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

use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;

interface FeedInterface
{
    public function getId(): int;

    public function setPersistMedia(bool $persistMedia): void;

    public function getPersistMedia(): bool;

    public function setPublishPostImmediately(bool $publishPostImmediately): void;

    public function getPublishPostImmediately(): bool;

    public function setConfiguration(?ConnectorFeedConfigurationInterface $configuration): void;

    public function getConfiguration(): ?ConnectorFeedConfigurationInterface;

    public function getCreationDate(): ?\DateTime;

    public function setCreationDate(\DateTime $date): void;

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void;

    public function getConnectorEngine(): ConnectorEngineInterface;

    public function setWall(WallInterface $wall): void;

    public function getWall();

    public function hasFeedTags(): bool;

    public function hasFeedTag(TagInterface $feedTag): bool;

    public function addFeedTag(TagInterface $feedTag): void;

    public function removeFeedTag(TagInterface $feedTag): void;

    /**
     * @return array<int, TagInterface>
     */
    public function getFeedTags(): iterable;
}
