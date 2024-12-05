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

namespace SocialDataBundle\Dto;

use SocialDataBundle\Model\SocialPostInterface;

class TransformData extends AbstractData
{
    protected SocialPostInterface $socialPostEntity;
    protected ?SocialPostInterface $transformedElement = null;

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

    public function getTransformedElement(): ?SocialPostInterface
    {
        return $this->transformedElement;
    }
}
