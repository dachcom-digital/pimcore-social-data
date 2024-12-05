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

namespace SocialDataBundle\Connector;

use SocialDataBundle\Dto\BuildConfig;
use SocialDataBundle\Dto\FetchData;
use SocialDataBundle\Dto\FilterData;
use SocialDataBundle\Dto\TransformData;
use SocialDataBundle\Exception\BuildException;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface SocialPostBuilderInterface
{
    /**
     * @throws BuildException
     */
    public function configureFetch(BuildConfig $buildConfig, OptionsResolver $resolver): void;

    /**
     * @throws BuildException
     */
    public function fetch(FetchData $data): void;

    /**
     * @throws BuildException
     */
    public function configureFilter(BuildConfig $buildConfig, OptionsResolver $resolver): void;

    /**
     * @throws BuildException
     */
    public function filter(FilterData $data): void;

    /**
     * @throws BuildException
     */
    public function configureTransform(BuildConfig $buildConfig, OptionsResolver $resolver): void;

    /**
     * @throws BuildException
     */
    public function transform(TransformData $data): void;
}
