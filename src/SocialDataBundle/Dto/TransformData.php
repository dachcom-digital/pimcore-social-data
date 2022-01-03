<?php

namespace SocialDataBundle\Dto;

use SocialDataBundle\Model\SocialPostInterface;

class TransformData extends AbstractData
{
    protected SocialPostInterface $socialPostEntity;
    protected SocialPostInterface $transformedElement;

    public function getTransferredData(): mixed
    {
        return $this->transferredData;
    }

    public function setSocialPostEntity(SocialPostInterface $socialPostEntity): void
    {
        $this->socialPostEntity = $socialPostEntity;
    }

    public function getSocialPostEntity(): SocialPostInterface
    {
        return $this->socialPostEntity;
    }

    public function setTransformedElement(SocialPostInterface $transformedElement): void
    {
        $this->transformedElement = $transformedElement;
    }

    public function getTransformedElement(): SocialPostInterface
    {
        return $this->transformedElement;
    }
}
