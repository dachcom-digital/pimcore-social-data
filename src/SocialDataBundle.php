<?php

namespace SocialDataBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use SocialDataBundle\DependencyInjection\Compiler\ConnectorDefinitionPass;
use SocialDataBundle\Tool\Install;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

class SocialDataBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public const PACKAGE_NAME = 'dachcom-digital/social-data';

    public function getInstaller(): Install
    {
        return $this->container->get(Install::class);
    }

    public function build(ContainerBuilder $container): void
    {
        $this->configureDoctrineExtension($container);

        $container->addCompilerPass(new ConnectorDefinitionPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    protected function configureDoctrineExtension(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createYamlMappingDriver(
                [$this->getNameSpacePath() => $this->getNamespaceName()],
                ['social_data.persistence.doctrine.manager'],
                'social_data.persistence.doctrine.enabled'
            )
        );
    }

    protected function getNamespaceName(): string
    {
        return 'SocialDataBundle\Model';
    }

    protected function getNameSpacePath(): string
    {
        return sprintf(
            '%s/config/doctrine/%s',
            $this->getPath(),
            'model'
        );
    }
}
