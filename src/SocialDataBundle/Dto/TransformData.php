<?php

namespace SocialDataBundle\Dto;

use SocialDataBundle\Model\SocialPostInterface;

class TransformData extends AbstractData
{
    /**
     * @var SocialPostInterface
     */
    protected $socialPostEntity;

    /**
     * @var SocialPostInterface
     */
    protected $transformedElement;

    /**
     * @return mixed
     */
    public function getTransferredData()
    {
        return $this->transferredData;
    }

    /**
     * @param SocialPostInterface $socialPostEntity
     */
    public function setSocialPostEntity(SocialPostInterface $socialPostEntity)
    {
        $this->socialPostEntity = $socialPostEntity;
    }

    /**
     * @return SocialPostInterface
     */
    public function getSocialPostEntity()
    {
        return $this->socialPostEntity;
    }

    /**
     * @param SocialPostInterface $transformedElement
     */
    public function setTransformedElement(SocialPostInterface $transformedElement)
    {
        $this->transformedElement = $transformedElement;
    }

    /**
     * @return SocialPostInterface
     */
    public function getTransformedElement()
    {
        return $this->transformedElement;
    }
}
