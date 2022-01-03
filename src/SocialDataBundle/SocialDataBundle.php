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

    public function build(ContainerBuilder $container)
    {
        $this->configureDoctrineExtension($container);

        $container->addCompilerPass(new ConnectorDefinitionPass());
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

    public function getCssPaths(): array
    {
        return [
            '/bundles/socialdata/css/admin.css'
        ];
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/socialdata/js/component/relation.js',
            '/bundles/socialdata/js/component/relationTextField.js',
            '/bundles/socialdata/js/component/connectWindow.js',
            '/bundles/socialdata/js/plugin.js',
            '/bundles/socialdata/js/settingsPanel.js',
            '/bundles/socialdata/js/wallsPanel.js',
            '/bundles/socialdata/js/wall/mainPanel.js',
            '/bundles/socialdata/js/connector/abstract-connector.js',
            '/bundles/socialdata/js/feed/abstract-feed.js',
            '/bundles/socialdata/js/vendor/dataObject.js',
        ];
    }

    protected function getNamespaceName(): string
    {
        return 'SocialDataBundle\Model';
    }

    protected function getNameSpacePath(): string
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            'model'
        );
    }
}
