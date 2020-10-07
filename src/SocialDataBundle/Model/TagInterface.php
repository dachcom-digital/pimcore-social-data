<?php

namespace SocialDataBundle\Model;

interface TagInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $type
     */
    public function setType(string $type);

    /**
     * @return string
     */
    public function getType();
}
