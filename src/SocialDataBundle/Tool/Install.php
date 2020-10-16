<?php

namespace SocialDataBundle\Tool;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Migrations\MigrationException;
use Doctrine\DBAL\Migrations\Version;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Db\Connection;
use Pimcore\Model\User\Permission;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Pimcore\Migrations\Migration\InstallMigration;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class Install extends MigrationInstaller
{
    /**
     * @var array
     */
    const REQUIRED_PERMISSION = [
        'social_data_bundle_menu_settings',
        'social_data_bundle_menu_walls',
    ];

    /**
     * @var TokenStorageUserResolver
     */
    protected $resolver;

    /**
     * @var DecoderInterface
     */
    protected $serializer;

    /**
     * @param TokenStorageUserResolver $resolver
     */
    public function setTokenStorageUserResolver(TokenStorageUserResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param DecoderInterface $serializer
     */
    public function setSerializer(DecoderInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion(): string
    {
        return '00000001';
    }

    /**
     * @throws MigrationException
     * @throws DBALException
     */
    protected function beforeInstallMigration()
    {
        $migrationConfiguration = $this->migrationManager->getBundleConfiguration($this->bundle);
        $this->migrationManager->markVersionAsMigrated($migrationConfiguration->getVersion($migrationConfiguration->getLatestVersion()));

        $this->initializeFreshSetup();
    }

    /**
     * @param Schema  $schema
     * @param Version $version
     */
    public function migrateInstall(Schema $schema, Version $version)
    {
        /** @var InstallMigration $migration */
        $migration = $version->getMigration();
        if ($migration->isDryRun()) {
            $this->outputWriter->write('<fg=cyan>DRY-RUN:</> Skipping installation');

            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }

    /**
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function initializeFreshSetup()
    {
        $this->installDbStructure();
        $this->installPermissions();
    }

    /**
     * @param Schema  $schema
     * @param Version $version
     */
    public function migrateUninstall(Schema $schema, Version $version)
    {
        /** @var InstallMigration $migration */
        $migration = $version->getMigration();
        if ($migration->isDryRun()) {
            $this->outputWriter->write('<fg=cyan>DRY-RUN:</> Skipping uninstallation');

            return;
        }

        // currently nothing to do.
    }

    /**
     * @param string|null $version
     */
    protected function beforeUpdateMigration(string $version = null)
    {
        // currently nothing to do.
        return;
    }

    /**
     * @throws DBALException
     */
    protected function installDbStructure()
    {
        /** @var Connection $db */
        $db = \Pimcore\Db::get();
        $db->query(file_get_contents($this->getInstallSourcesPath() . '/sql/install.sql'));
    }

    /**
     * @throws AbortMigrationException
     */
    protected function installPermissions()
    {
        foreach (self::REQUIRED_PERMISSION as $permission) {
            $definition = Permission\Definition::getByKey($permission);

            if ($definition) {
                $this->outputWriter->write(sprintf(
                    '     <comment>WARNING:</comment> Skipping permission "%s" as it already exists',
                    $permission
                ));

                continue;
            }

            try {
                Permission\Definition::create($permission);
            } catch (\Throwable $e) {
                throw new AbortMigrationException(sprintf('Failed to create permission "%s": %s', $permission, $e->getMessage()));
            }
        }
    }

    /**
     * @return string
     */
    protected function getInstallSourcesPath()
    {
        return __DIR__ . '/../Resources/install';
    }
}
