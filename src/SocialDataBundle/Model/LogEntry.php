<?php

namespace SocialDataBundle\Model;

class LogEntry implements LogEntryInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ConnectorEngineInterface
     */
    protected $connectorEngine;

    /**
     * @var FeedInterface
     */
    protected $feed;

    /**
     * @var WallInterface
     */
    protected $wall;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine)
    {
        $this->connectorEngine = $connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectorEngine()
    {
        return $this->connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function setWall(WallInterface $wall)
    {
        $this->wall = $wall;
    }

    /**
     * {@inheritdoc}
     */
    public function getWall()
    {
        return $this->wall;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeed(FeedInterface $feed)
    {
        $this->feed = $feed;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationDate(\DateTime $date)
    {
        $this->creationDate = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
}
