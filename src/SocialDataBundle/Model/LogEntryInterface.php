<?php

namespace SocialDataBundle\Model;

interface LogEntryInterface
{
    public function getId(): int;

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void;

    public function getConnectorEngine(): ConnectorEngineInterface;

    public function setWall(WallInterface $wall): void;

    public function getWall(): WallInterface;

    public function setFeed(FeedInterface $feed): void;

    public function getFeed(): FeedInterface;

    public function getType(): string;

    public function setType(string $type): void;

    public function getMessage(): string;

    public function setMessage(string $message): void;

    public function getCreationDate(): \DateTime;

    public function setCreationDate(\DateTime $date): void;
}
