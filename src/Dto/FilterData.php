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

class FilterData extends AbstractData
{
    protected ?array $filteredElement = null;
    protected string|int|null $filteredId = null;

    public function getTransferredData(): mixed
    {
        return $this->transferredData;
    }

    public function setFilteredElement(array $filteredElement): void
    {
        $this->filteredElement = $filteredElement;
    }

    public function getFilteredElement(): ?array
    {
        return $this->filteredElement;
    }

    public function setFilteredId(string|int $filteredId): void
    {
        $this->filteredId = $filteredId;
    }

    public function getFilteredId(): string|int|null
    {
        return $this->filteredId;
    }
}
