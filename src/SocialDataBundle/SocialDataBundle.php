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

    const PACKAGE_NAME = 'dachcom-digital/social-data';

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Install::class);
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->configureDoctrineExtension($container);

        $container->addCompilerPass(new ConnectorDefinitionPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    /**
     * @param ContainerBuilder $container
     */
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

    /**
     * @return array
     */
    public function getCssPaths()
    {
        return [
            '/bundles/socialdata/css/admin.css'
        ];
    }

    /**
     * @return string[]
     */
    public function getJsPaths()
    {
        return [
            '/bundles/socialdata/js/component/relation.js',
            '/bundles/socialdata/js/component/relationTextField.js',

            '/bundles/socialdata/js/plugin.js',
            '/bundles/socialdata/js/settingsPanel.js',
            '/bundles/socialdata/js/wallsPanel.js',
            '/bundles/socialdata/js/wall/mainPanel.js',

            '/bundles/socialdata/js/connector/abstract-connector.js',
            '/bundles/socialdata/js/connector/facebook-connector.js',
            '/bundles/socialdata/js/feed/abstract-feed.js',
            '/bundles/socialdata/js/feed/facebook-feed.js',
        ];
    }

    /**
     * @return string|null
     */
    protected function getNamespaceName()
    {
        return 'SocialDataBundle\Model';
    }

    /**
     * @return string
     */
    protected function getNameSpacePath()
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            'model'
        );
    }
}
