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
     * @var int
     */
    protected $objectId;

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
     * {@inheritdoc}
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
    public function setObjectId(int $objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectId()
    {
        return $this->objectId;
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
