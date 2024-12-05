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

class LogEntry implements LogEntryInterface
{
    protected int $id;
    protected ConnectorEngineInterface $connectorEngine;
    protected FeedInterface $feed;
    protected WallInterface $wall;
    protected string $type;
    protected string $message;
    protected \DateTime $creationDate;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void
    {
        $this->connectorEngine = $connectorEngine;
    }

    public function getConnectorEngine(): ConnectorEngineInterface
    {
        return $this->connectorEngine;
    }

    public function setWall(WallInterface $wall): void
    {
        $this->wall = $wall;
    }

    public function getWall(): WallInterface
    {
        return $this->wall;
    }

    public function setFeed(FeedInterface $feed): void
    {
        $this->feed = $feed;
    }

    public function getFeed(): FeedInterface
    {
        return $this->feed;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setCreationDate(\DateTime $date): void
    {
        $this->creationDate = $date;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }
}
