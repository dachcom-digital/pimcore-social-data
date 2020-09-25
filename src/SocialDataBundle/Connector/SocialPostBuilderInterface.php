<?php

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
     * @param BuildConfig     $buildConfig
     * @param OptionsResolver $resolver
     *
     * @throws BuildException
     */
    public function configureFetch(BuildConfig $buildConfig, OptionsResolver $resolver): void;

    /**
     * @param FetchData $data
     *
     * @throws BuildException
     */
    public function fetch(FetchData $data): void;

    /**
     * @param BuildConfig     $buildConfig
     * @param OptionsResolver $resolver
     *
     * @throws BuildException
     */
    public function configureFilter(BuildConfig $buildConfig, OptionsResolver $resolver): void;

    /**
     * @param FilterData $data
     *
     * @throws BuildException
     */
    public function filter(FilterData $data): void;

    /**
     * @param BuildConfig     $buildConfig
     * @param OptionsResolver $resolver
     *
     * @throws BuildException
     */
    public function configureTransform(BuildConfig $buildConfig, OptionsResolver $resolver): void;

    /**
     * @param TransformData $data
     *
     * @throws BuildException
     */
    public function transform(TransformData $data): void;
}
