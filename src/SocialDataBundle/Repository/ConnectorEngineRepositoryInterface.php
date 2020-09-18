<?php

namespace SocialDataBundle\Repository;

use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorEngineRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return null|ConnectorEngineInterface
     */
    public function findById($id);

    /**
     * @param string $name
     *
     * @return null|ConnectorEngineInterface
     */
    public function findByName(string $name);

    /**
     * @param string $name
     *
     * @return null|ConnectorEngineInterface
     */
    public function findIdByName(string $name);

    /**
     * @return ConnectorEngineInterface[]
     */
    public function findAll();
}
