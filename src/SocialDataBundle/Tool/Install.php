<?php

namespace SocialDataBundle\Tool;

use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Db\Connection;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Model\User\Permission;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class Install extends SettingsStoreAwareInstaller
{
    public const REQUIRED_PERMISSION = [
        'social_data_bundle_menu_settings',
        'social_data_bundle_menu_walls',
    ];

    protected TokenStorageUserResolver $resolver;
    protected DecoderInterface $serializer;

    public function setTokenStorageUserResolver(TokenStorageUserResolver $resolver): void
    {
        $this->resolver = $resolver;
    }

    public function setSerializer(DecoderInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function install(): void
    {
        $this->installDbStructure();
        $this->installPermissions();

        parent::install();
    }

    protected function installDbStructure(): void
    {
        /** @var Connection $db */
        $db = \Pimcore\Db::get();
        $db->query(file_get_contents($this->getInstallSourcesPath() . '/sql/install.sql'));
    }

    protected function installPermissions(): void
    {
        foreach (self::REQUIRED_PERMISSION as $permission) {
            $definition = Permission\Definition::getByKey($permission);

            if ($definition) {
                $this->output->write(sprintf(
                    '     <comment>WARNING:</comment> Skipping permission "%s" as it already exists',
                    $permission
                ));

                continue;
            }

            try {
                Permission\Definition::create($permission);
            } catch (\Throwable $e) {
                throw new InstallationException(sprintf('Failed to create permission "%s": %s', $permission, $e->getMessage()));
            }
        }
    }

    protected function getInstallSourcesPath(): string
    {
        return __DIR__ . '/../Resources/install';
    }
}
