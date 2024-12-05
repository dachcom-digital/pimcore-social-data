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

namespace SocialDataBundle\Factory;

use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Service\EnvironmentService;

class SocialPostFactory implements SocialPostFactoryInterface
{
    public function __construct(protected EnvironmentService $environmentService)
    {
    }

    public function create(): SocialPostInterface
    {
        $objectClass = sprintf('\Pimcore\Model\DataObject\%s', ucfirst($this->environmentService->getSocialPostDataClass()));

        return new $objectClass();
    }
}
