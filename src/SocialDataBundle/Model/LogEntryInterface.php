<?php

namespace SocialDataBundle\Model;

interface LogEntryInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param ConnectorEngineInterface $connectorEngine
     */
    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine);

    /**
     * @return ConnectorEngineInterface
     */
    public function getConnectorEngine();

    /**
     * @param int $objectId
     */
    public function setObjectId(int $objectId);

    /**
     * @return int
     */
    public function getObjectId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType(string $type);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     */
    public function setMessage(string $message);

    /**
     * @return \DateTime
     */
    public function getCreationDate();

    /**
     * @param \DateTime $date
     */
    public function setCreationDate(\DateTime $date);
}
